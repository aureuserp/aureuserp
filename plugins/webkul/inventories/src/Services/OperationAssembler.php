<?php

namespace Webkul\Inventory\Services;

use Illuminate\Support\Collection;
use Webkul\Inventory\Enums\OperationState;
use Webkul\Inventory\Models\Move;
use Webkul\Inventory\Models\Operation;

class OperationAssembler
{
    public function assignMovesToOperations(Collection $moves): void
    {
        if ($moves->isEmpty()) {
            return;
        }

        $grouped = $moves->groupBy(fn (Move $move) => implode('_', $move->operationGroupingKey()));

        foreach ($grouped as $group) {
            $operation = $this->findReusableOperation($group->first());

            if ($operation) {
                $this->relaxSharedFields($operation, $group);
            } else {
                $group = $group->filter(
                    fn (Move $move) => float_compare($move->product_uom_qty, 0.0, precisionRounding: $move->uom->rounding) >= 0
                );

                if ($group->isEmpty()) {
                    continue;
                }

                $operation = Operation::create($this->buildOperationAttributes($group));
            }

            $group->each(fn (Move $move) => $move->update(['operation_id' => $operation->id]));

            $operation->refresh();

            $operation->computeState();

            $operation->save();
        }
    }

    public function findReusableOperation(Move $move): ?Operation
    {
        return Operation::query()
            ->where('procurement_group_id', $move->procurement_group_id)
            ->where('source_location_id', $move->source_location_id)
            ->where('destination_location_id', $move->destination_location_id ?? $move->operationType->destination_location_id)
            ->where('operation_type_id', $move->operation_type_id)
            ->whereIn('state', [OperationState::DRAFT, OperationState::CONFIRMED, OperationState::ASSIGNED])
            ->when(
                $move->partner_id && ! $move->procurement_group_id,
                fn ($query) => $query->where('partner_id', $move->partner_id)
            )
            ->first();
    }

    public function buildOperationAttributes(Collection $moves): array
    {
        $partners = $moves->pluck('partner_id')->unique();

        $attributes = [
            'origin'               => $this->mergedOrigin($moves),
            'company_id'           => $moves->pluck('company_id')->first(),
            'user_id'              => null,
            'procurement_group_id' => $moves->pluck('procurement_group_id')->first(),
            'partner_id'           => $partners->count() === 1 ? $partners->first() : null,
            'operation_type_id'    => $moves->pluck('operation_type_id')->first(),
            'source_location_id'   => $moves->pluck('source_location_id')->first(),
        ];

        $destinationIds = $moves->pluck('destination_location_id')->filter()->unique();

        if ($destinationIds->isNotEmpty()) {
            $attributes['destination_location_id'] = $destinationIds->first();
        }

        if ($saleOrderId = $moves->first()?->procurementGroup?->sale_order_id) {
            $attributes['sale_order_id'] = $saleOrderId;
        }

        return $attributes;
    }

    protected function mergedOrigin(Collection $moves): ?string
    {
        $origins = $moves->pluck('origin')->filter()->unique()->values();

        if ($origins->isEmpty()) {
            return null;
        }

        return $origins->take(5)->implode(',').($origins->count() > 5 ? '...' : '');
    }

    protected function relaxSharedFields(Operation $operation, Collection $moves): void
    {
        $attributes = [];

        if ($moves->some(fn (Move $move) => $operation->partner_id !== $move->partner_id)) {
            $attributes['partner_id'] = null;
        }

        if ($moves->some(fn (Move $move) => $operation->origin !== $move->origin)) {
            $attributes['origin'] = null;
        }

        if ($attributes !== []) {
            $operation->update($attributes);
        }
    }
}
