<?php

namespace Webkul\Inventory\Services;

use Illuminate\Support\Collection;
use Webkul\Inventory\Enums\GroupPropagation;
use Webkul\Inventory\Enums\ProcureMethod;
use Webkul\Inventory\Enums\RuleAction;
use Webkul\Inventory\Models\Location;
use Webkul\Inventory\Models\Move;
use Webkul\Inventory\Models\Product;
use Webkul\Inventory\Models\Rule;
use Webkul\Inventory\Support\ProcurementRequest;
use Webkul\Inventory\Support\StockScope;
use Webkul\Product\Enums\ProductType;

class ProcurementRunner
{
    public function run(Collection $requests): void
    {
        if ($requests->isEmpty()) {
            return;
        }

        $byAction = [];

        $errors = [];

        foreach ($requests as $request) {
            if (
                $request->product->type !== ProductType::GOODS
                || float_is_zero($request->quantity, precisionRounding: $request->uom->rounding)
            ) {
                continue;
            }

            $rule = app(RuleResolver::class)->findRule($request->product, $request->location, [
                'routes'    => $request->options->routeSet(),
                'warehouse' => $request->options->targetWarehouse(),
            ]);

            if (! $rule) {
                $errors[] = __('inventories::system.inventory-manager.run-procurement.no-rule-found', [
                    'product'  => $request->product->name,
                    'location' => $request->location->full_name,
                ]);

                continue;
            }

            $action = $rule->action === RuleAction::PULL_PUSH ? RuleAction::PULL : $rule->action;

            $byAction[$action->value][] = [$request, $rule];
        }

        foreach ($byAction as $action => $pairs) {
            try {
                $this->dispatch(RuleAction::from($action), collect($pairs));
            } catch (\Exception $exception) {
                $errors[] = $exception->getMessage();
            }
        }

        if ($errors !== []) {
            throw new \Exception(implode("\n", $errors));
        }
    }

    public function shortfallQuantities(Collection $moves): array
    {
        $optionalMoveIds = [];

        $productsPerLocation = [];

        foreach ($moves as $move) {
            if ($move->rule?->procure_method !== ProcureMethod::MTS_ELSE_MTO) {
                continue;
            }

            $optionalMoveIds[$move->id] = true;

            $productsPerLocation[$move->source_location_id][] = $move->product_id;
        }

        $freeStock = $this->freeStockPerLocation($productsPerLocation);

        $quantities = [];

        foreach ($moves as $move) {
            $rounding = $move->product->uom->rounding ?? 0.01;

            $coveredByStock = isset($optionalMoveIds[$move->id])
                && float_compare($move->product_qty, 0, precisionRounding: $rounding) > 0
                && ! $move->bypassesReservation();

            if (! $coveredByStock) {
                $quantities[] = $move->product_uom_qty;

                continue;
            }

            $available = max($freeStock[$move->source_location_id][$move->product_id] ?? 0, 0);

            $quantities[] = $move->product->uom->computeQuantity(
                max($move->product_qty - $available, 0),
                $move->uom,
                roundingMethod: 'HALF-UP'
            );

            $freeStock[$move->source_location_id][$move->product_id] =
                ($freeStock[$move->source_location_id][$move->product_id] ?? 0)
                - min($move->product_qty, $available);
        }

        return $quantities;
    }

    protected function freeStockPerLocation(array $productsPerLocation): array
    {
        $freeStock = [];

        foreach ($productsPerLocation as $locationId => $productIds) {
            $location = Location::find($locationId);

            if (! $location || $location->bypassesReservation()) {
                continue;
            }

            $freeStock[$locationId] = Product::query()
                ->whereIn('id', array_unique($productIds))
                ->get()
                ->mapWithKeys(fn (Product $product) => [
                    $product->id => $product->withStockScope(StockScope::make()->forLocations($locationId))->free_qty,
                ])
                ->all();
        }

        return $freeStock;
    }

    protected function dispatch(RuleAction $action, Collection $pairs): void
    {
        match ($action) {
            RuleAction::PULL        => $this->runPullRules($pairs),
            RuleAction::BUY         => $this->runBuyRules($pairs),
            RuleAction::MANUFACTURE => $this->runManufactureRules($pairs),
            default                 => null,
        };
    }

    protected function runPullRules(Collection $pairs): void
    {
        foreach ($pairs as [$request, $rule]) {
            if (! $rule->source_location_id) {
                throw new \Exception(__('inventories::system.inventory-manager.run-procurement.no-source-location', [
                    'name' => $rule->name,
                ]));
            }
        }

        $byCompany = [];

        foreach ($pairs as [$request, $rule]) {
            $attributes = $this->buildMoveAttributes($rule, $request);

            $attributes['procure_method'] = $rule->procure_method === ProcureMethod::MTS_ELSE_MTO
                ? ProcureMethod::MAKE_TO_STOCK
                : $rule->procure_method;

            $byCompany[$request->company?->id][] = $attributes;
        }

        foreach ($byCompany as $attributeSets) {
            $moves = collect();

            foreach ($attributeSets as $attributes) {
                $destinations = $attributes['move_destinations'];

                unset($attributes['move_destinations']);

                $move = Move::create($attributes);

                if ($destinations->isNotEmpty()) {
                    $move->moveDestinations()->attach($destinations->pluck('id')->all());
                }

                $moves->push($move);
            }

            app(MoveConfirmer::class)->confirm($moves);
        }
    }

    protected function runBuyRules(Collection $pairs): void {}

    protected function runManufactureRules(Collection $pairs): void {}

    public function buildMoveAttributes(Rule $rule, ProcurementRequest $request): array
    {
        $options = $request->options;

        $scheduledAt = ($options->plannedDate() ?? now())->copy()->subDays($rule->delay ?? 0);

        $deadline = $options->deadlineDate()?->copy()->subDays($rule->delay ?? 0);

        $partner = $rule->partnerAddress ?? $options->procurementGroup()?->partner;

        $attributes = [
            'name'                 => substr($request->name, 0, 2000),
            'company_id'           => $rule->company_id
                ?? $request->location->company_id
                ?? $rule->destinationLocation?->company_id
                ?? $request->company?->id,
            'product_id'             => $request->product->id,
            'uom_id'                 => $request->uom->id,
            'product_uom_qty'        => $request->quantity,
            'product_qty'            => $request->uom->computeQuantity($request->quantity, $request->product->uom, roundingMethod: 'HALF-UP'),
            'partner_id'             => $partner?->id,
            'source_location_id'     => $rule->source_location_id,
            'final_location_id'      => $rule->destination_location_id,
            'move_destinations'      => $options->destinationMoves(),
            'rule_id'                => $rule->id,
            'procure_method'         => $rule->procure_method,
            'origin'                 => $request->origin,
            'operation_type_id'      => $rule->operation_type_id,
            'procurement_group_id'   => $this->propagatedGroupId($rule, $options->procurementGroup()?->id),
            'warehouse_id'           => $rule->warehouse_id,
            'scheduled_at'           => $scheduledAt,
            'deadline'               => $rule->group_propagation_option === GroupPropagation::FIXED ? null : $deadline,
            'description_picking'    => $request->product->descriptionFor($rule->operationType),
            'product_packaging_id'   => $options->productPackaging()?->id,
        ];

        $optionalLinks = [
            'sale_order_line_id'     => $options->saleOrderLineId(),
            'purchase_order_line_id' => $options->purchaseOrderLineId(),
            'work_order_id'          => $options->workOrderId(),
            'bom_line_id'            => $options->bomLineId(),
        ];

        foreach ($optionalLinks as $column => $value) {
            if ($value !== null) {
                $attributes[$column] = $value;
            }
        }

        if ($rule->location_dest_from_rule) {
            $attributes['destination_location_id'] = $rule->destination_location_id;
        }

        return $attributes;
    }

    protected function propagatedGroupId(Rule $rule, ?int $requestedGroupId): ?int
    {
        return match ($rule->group_propagation_option) {
            GroupPropagation::PROPAGATE => $requestedGroupId,
            GroupPropagation::FIXED     => $rule->procurement_group_id,
            default                     => null,
        };
    }
}
