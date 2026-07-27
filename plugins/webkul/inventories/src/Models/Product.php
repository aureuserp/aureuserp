<?php

namespace Webkul\Inventory\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Webkul\Field\Traits\HasCustomFields;
use Webkul\Inventory\Database\Factories\ProductFactory;
use Webkul\Inventory\Enums\MoveState;
use Webkul\Inventory\Enums\OperationType as OperationTypeEnum;
use Webkul\Inventory\Enums\ProcureMethod;
use Webkul\Inventory\Enums\ProductTracking;
use Webkul\Inventory\Enums\RuleAction;
use Webkul\Inventory\Facades\Inventory as InventoryFacade;
use Webkul\Product\Models\Product as BaseProduct;
use Webkul\Security\Models\User;

class Product extends BaseProduct
{
    use HasCustomFields;

    public function __construct(array $attributes = [])
    {
        $this->mergeFillable([
            'sale_delay',
            'tracking',
            'description_picking',
            'description_pickingout',
            'description_pickingin',
            'is_storable',
            'expiration_time',
            'use_time',
            'removal_time',
            'alert_time',
            'use_expiration_date',
            'responsible_id',
        ]);

        $this->mergeCasts([
            'tracking'            => ProductTracking::class,
            'use_expiration_date' => 'boolean',
            'is_storable'         => 'boolean',
        ]);

        parent::__construct($attributes);
    }

    protected array $context = [];

    public function setContext(array $context)
    {
        $this->context = array_merge($this->context, $context);

        return $this;
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function routes(): BelongsToMany
    {
        return $this->belongsToMany(Route::class, 'inventories_product_routes', 'product_id', 'route_id');
    }

    public function variants(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function quantities(): HasMany
    {
        if ($this->is_configurable) {
            return $this->hasMany(ProductQuantity::class)
                ->orWhereIn('product_id', $this->variants()->pluck('id'));
        } else {
            return $this->hasMany(ProductQuantity::class);
        }
    }

    public function moves(): HasMany
    {
        if ($this->is_configurable) {
            return $this->hasMany(Move::class)
                ->orWhereIn('product_id', $this->variants()->pluck('id'));
        } else {
            return $this->hasMany(Move::class);
        }
    }

    public function moveLines(): HasMany
    {
        if ($this->is_configurable) {
            return $this->hasMany(MoveLine::class)
                ->orWhereIn('product_id', $this->variants()->pluck('id'));
        } else {
            return $this->hasMany(MoveLine::class);
        }
    }

    public function storageCategoryCapacities(): BelongsToMany
    {
        return $this->belongsToMany(StorageCategoryCapacity::class, 'inventories_storage_category_capacities', 'storage_category_id', 'package_type_id');
    }

    public function orderPoints(): HasMany
    {
        return $this->hasMany(OrderPoint::class);
    }

    public function responsible(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getAvailableQtyAttribute(): float
    {
        $quantities = $this->computeQuantities();

        return $quantities['available_qty'] ?? 0.0;
    }

    public function getFreeQtyAttribute(): float
    {
        $quantities = $this->computeQuantities();

        return $quantities['free_qty'] ?? 0.0;
    }

    public function getIncomingQtyAttribute(): float
    {
        $quantities = $this->computeQuantities();

        return $quantities['incoming_qty'] ?? 0.0;
    }

    public function getOutgoingQtyAttribute(): float
    {
        $quantities = $this->computeQuantities();

        return $quantities['outgoing_qty'] ?? 0.0;
    }

    public function getVirtualAvailableQtyAttribute(): float
    {
        $quantities = $this->computeQuantities();

        return $quantities['virtual_available_qty'] ?? 0.0;
    }

    public function getDescription(OperationType $operationType): ?string
    {
        return match ($operationType->type) {
            OperationTypeEnum::INCOMING => $this->description_pickingin ?? $this->description,
            OperationTypeEnum::OUTGOING => $this->description_pickingout ?? $this->name,
            OperationTypeEnum::INTERNAL => $this->description_picking ?? $this->description,
            default                     => $this->description,
        };
    }

    public function getRulesFromLocation($location, $routes = false, $seenRules = null)
    {
        if (! $seenRules) {
            $seenRules = collect();
        }

        $warehouse = $location->warehouse ?? null;

        if (! $warehouse && $seenRules->isNotEmpty()) {
            $warehouse = optional($seenRules->last())->propagateWarehouse;
        }

        if (! $routes) {
            $routes = collect();
        }

        $rule = InventoryFacade::getRule($this, $location, [
            'routes'    => $routes,
            'warehouse' => $warehouse,
        ]);

        if ($rule && $seenRules->contains(fn ($seenRule) => $seenRule->id === $rule->id)) {
            throw new \Exception(__(
                'inventories::system.product.endless-loop-rule',
                ['name' => $rule->name]
            ));
        }

        if (! $rule) {
            return $seenRules;
        }

        $updatedSeenRules = $seenRules->push($rule);

        if (
            $rule->procure_method === ProcureMethod::MAKE_TO_STOCK
            || ! in_array($rule->action, [RuleAction::PULL_PUSH, RuleAction::PULL], true)
        ) {
            return $updatedSeenRules;
        }

        return $this->getRulesFromLocation(
            $rule->sourceLocation,
            false,
            $updatedSeenRules
        );
    }

    public function getDatesInfo($date, $location, $routeIds = false): array
    {
        $rules = $this->getRulesFromLocation($location, $routeIds);

        [$delays] = $rules->getLeadDays($this);

        return [
            'date_planned' => Carbon::parse($date)->subDays($delays['security_lead_days']),
            'date_order'   => Carbon::parse($date)->subDays($delays['security_lead_days'] + $delays['purchase_delay']),
        ];
    }

    public function computeQuantities(): array
    {
        $lotId = $this->context['lot_id'] ?? null;
        $packageId = $this->context['package_id'] ?? null;
        $fromDate = $this->context['from_date'] ?? null;
        $toDate = $this->context['to_date'] ?? null;

        if ($this->is_configurable) {
            $totals = [
                'available_qty'         => 0.0,
                'free_qty'              => 0.0,
                'incoming_qty'          => 0.0,
                'outgoing_qty'          => 0.0,
                'virtual_available_qty' => 0.0,
            ];

            foreach ($this->variants as $variant) {
                $variant->context = $this->context ?? [];

                $variantQty = $variant->computeQuantities();

                foreach ($totals as $key => $_) {
                    $totals[$key] += $variantQty[$key];
                }
            }

            return array_map(fn ($value) => float_round($value, precisionRounding: $this->uom->rounding), $totals);
        }

        [$quantLocationScope, $moveInLocationScope, $moveOutLocationScope] = $this->getLocationFilters();

        $toDate = $toDate ? Carbon::parse($toDate) : null;

        $datesInThePast = $toDate && $toDate->lt(now());

        $todoStates = [MoveState::WAITING, MoveState::CONFIRMED, MoveState::ASSIGNED, MoveState::PARTIALLY_ASSIGNED];

        $movesInRes = Move::query()
            ->where('product_id', $this->id)
            ->whereIn('state', $todoStates)
            ->where(fn (Builder $q) => $moveInLocationScope($q))
            ->when($fromDate, fn ($q) => $q->where('scheduled_at', '>=', $fromDate))
            ->when($toDate, fn ($q) => $q->where('scheduled_at', '<=', $toDate))
            ->groupBy('product_id')
            ->selectRaw('product_id, SUM(product_qty) as total')
            ->value('total') ?? 0.0;

        $movesOutRes = Move::query()
            ->where('product_id', $this->id)
            ->whereIn('state', $todoStates)
            ->where(fn (Builder $q) => $moveOutLocationScope($q))
            ->when($fromDate, fn ($q) => $q->where('scheduled_at', '>=', $fromDate))
            ->when($toDate, fn ($q) => $q->where('scheduled_at', '<=', $toDate))
            ->groupBy('product_id')
            ->selectRaw('product_id, SUM(product_qty) as total')
            ->value('total') ?? 0.0;

        $quantRow = ProductQuantity::query()
            ->where('product_id', $this->id)
            ->where(fn (Builder $q) => $quantLocationScope($q))
            ->when($lotId !== null, fn ($q) => $q->where('lot_id', $lotId))
            ->when($packageId !== null, fn ($q) => $q->where('package_id', $packageId))
            ->selectRaw('SUM(quantity) as quantity, SUM(reserved_quantity) as reserved_quantity')
            ->first();

        $qtyAvailableBase = $quantRow->quantity ?? 0.0;
        $reservedQuantity = $quantRow->reserved_quantity ?? 0.0;

        $movesInResPast = 0.0;
        $movesOutResPast = 0.0;

        if ($datesInThePast) {
            Move::query()
                ->where('product_id', $this->id)
                ->where('state', MoveState::DONE)
                ->where('scheduled_at', '>', $toDate)
                ->where(fn (Builder $q) => $moveInLocationScope($q))
                ->groupBy('product_id', 'uom_id')
                ->selectRaw('uom_id, SUM(quantity) as total')
                ->get()
                ->each(function ($row) use (&$movesInResPast) {
                    $movesInResPast += $row->uom->computeQuantity($row->total, $this->uom);
                });

            Move::query()
                ->where('product_id', $this->id)
                ->where('state', MoveState::DONE)
                ->where('scheduled_at', '>', $toDate)
                ->where(fn (Builder $q) => $moveOutLocationScope($q))
                ->groupBy('product_id', 'uom_id')
                ->selectRaw('uom_id, SUM(quantity) as total')
                ->get()
                ->each(function ($row) use (&$movesOutResPast) {
                    $movesOutResPast += $row->uom->computeQuantity($row->total, $this->uom);
                });
        }

        $rounding = $this->uom->rounding;

        $qtyAvailable = $datesInThePast
            ? $qtyAvailableBase - $movesInResPast + $movesOutResPast
            : $qtyAvailableBase;

        $incomingQty = float_round($movesInRes, precisionRounding: $rounding);
        $outgoingQty = float_round($movesOutRes, precisionRounding: $rounding);

        return [
            'available_qty'         => float_round($qtyAvailable, precisionRounding: $rounding),
            'free_qty'              => float_round($qtyAvailable - $reservedQuantity, precisionRounding: $rounding),
            'incoming_qty'          => $incomingQty,
            'outgoing_qty'          => $outgoingQty,
            'virtual_available_qty' => float_round($qtyAvailable + $incomingQty - $outgoingQty, precisionRounding: $rounding),
        ];
    }

    public function getLocationFilters(): array
    {
        $locationId = $this->context['location_id'] ?? null;
        $warehouseId = $this->context['warehouse_id'] ?? null;
        $companyIds = $this->context['company_ids'] ?? [];
        $strict = $this->context['strict'] ?? false;

        if (empty($companyIds)) {
            $companyIds = array_filter([Auth::user()?->default_company_id]);
        }

        $searchIds = function (string $modelClass, array $values): array {
            $ids = [];
            $names = [];

            foreach ($values as $item) {
                if (is_int($item) || ctype_digit((string) $item)) {
                    $ids[] = (int) $item;
                } else {
                    $names[] = $item;
                }
            }

            if (! empty($names)) {
                $query = $modelClass::query();

                $query->where(function (Builder $query) use ($names) {
                    foreach ($names as $name) {
                        $query->orWhere('name', 'like', '%'.$name.'%');
                    }
                });

                $ids = array_merge($ids, $query->pluck('id')->toArray());
            }

            return array_values(array_unique($ids));
        };

        if ($locationId !== null && ! is_array($locationId)) {
            $locationId = [$locationId];
        }

        if ($warehouseId !== null && ! is_array($warehouseId)) {
            $warehouseId = [$warehouseId];
        }

        if (! empty($warehouseId)) {
            $warehouseIds = $searchIds(Warehouse::class, $warehouseId);

            $warehouseLocationIds = Warehouse::query()
                ->whereIn('id', $warehouseIds)
                ->pluck('view_location_id')
                ->filter()
                ->unique()
                ->values()
                ->toArray();

            if (! empty($locationId)) {
                $locationIds = $searchIds(Location::class, $locationId);

                $parentPaths = Location::query()
                    ->whereIn('id', $warehouseLocationIds)
                    ->pluck('parent_path')
                    ->filter()
                    ->values()
                    ->toArray();

                $resolvedLocationIds = Location::query()
                    ->whereIn('id', $locationIds)
                    ->get(['id', 'parent_path'])
                    ->filter(function ($location) use ($parentPaths) {
                        foreach ($parentPaths as $parentPath) {
                            if (
                                ! empty($location->parent_path)
                                && str_starts_with($location->parent_path, $parentPath)
                            ) {
                                return true;
                            }
                        }

                        return false;
                    })
                    ->pluck('id')
                    ->unique()
                    ->values()
                    ->toArray();
            } else {
                $resolvedLocationIds = $warehouseLocationIds;
            }
        } elseif (! empty($locationId)) {
            $resolvedLocationIds = $searchIds(Location::class, $locationId);
        } else {
            $resolvedLocationIds = Warehouse::query()
                ->whereIn('company_id', $companyIds)
                ->pluck('view_location_id')
                ->filter()
                ->unique()
                ->values()
                ->toArray();
        }

        return $this->getLocationFiltersNew($resolvedLocationIds, $strict);
    }

    protected function getLocationFiltersNew(array $locationIds, bool $strict = false): array
    {
        if (empty($locationIds)) {
            $impossible = fn (Builder $query) => $query->whereRaw('0 = 1');

            return [$impossible, $impossible, $impossible];
        }

        $locations = Location::query()
            ->whereIn('id', $locationIds)
            ->get(['id', 'parent_path']);

        if ($locations->isEmpty()) {
            $impossible = fn (Builder $query) => $query->whereRaw('0 = 1');

            return [$impossible, $impossible, $impossible];
        }

        if ($strict) {
            $ids = $locations->pluck('id')->values()->toArray();

            $quantityScope = fn (Builder $query) => $query->whereIn('location_id', $ids);

            $moveSourceScope = fn (Builder $query) => $query->whereIn('source_location_id', $ids);

            $moveDestinationScope = fn (Builder $query) => $query->whereIn('destination_location_id', $ids);
        } else {
            $parentPaths = $locations
                ->pluck('parent_path')
                ->filter()
                ->values()
                ->toArray();

            if (empty($parentPaths)) {
                $impossible = fn (Builder $query) => $query->whereRaw('0 = 1');

                return [$impossible, $impossible, $impossible];
            }

            $matchingLocationIds = Location::query()
                ->where(function (Builder $query) use ($parentPaths) {
                    foreach ($parentPaths as $path) {
                        $query->orWhere('parent_path', 'like', $path.'%');
                    }
                })
                ->pluck('id')
                ->toArray();

            $quantityScope = fn (Builder $query) => $query->whereIn('location_id', $matchingLocationIds);

            $moveSourceScope = fn (Builder $query) => $query->whereIn('source_location_id', $matchingLocationIds);

            $moveDestinationScope = fn (Builder $query) => $query->where(function (Builder $q) use ($matchingLocationIds) {
                $q->where(function (Builder $q2) use ($matchingLocationIds) {
                    $q2->whereNotNull('final_location_id')
                        ->whereIn('final_location_id', $matchingLocationIds);
                })->orWhere(function (Builder $q2) use ($matchingLocationIds) {
                    $q2->whereNull('final_location_id')
                        ->whereIn('destination_location_id', $matchingLocationIds);
                });
            });
        }

        $moveInScope = fn (Builder $query) => $query
            ->where(fn (Builder $q) => $moveDestinationScope($q))
            ->whereNot(fn (Builder $q) => $moveSourceScope($q));

        $moveOutScope = fn (Builder $query) => $query
            ->where(fn (Builder $q) => $moveSourceScope($q))
            ->whereNot(fn (Builder $q) => $moveDestinationScope($q));

        return [$quantityScope, $moveInScope, $moveOutScope];
    }

    protected static function newFactory(): ProductFactory
    {
        return ProductFactory::new();
    }
}
