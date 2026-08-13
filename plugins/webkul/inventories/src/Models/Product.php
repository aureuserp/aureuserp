<?php

namespace Webkul\Inventory\Models;

use Carbon\Carbon;
use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Webkul\Field\Traits\HasCustomFields;
use Webkul\Inventory\Database\Factories\ProductFactory;
use Webkul\Inventory\Enums\MoveState;
use Webkul\Inventory\Enums\OperationType as OperationTypeEnum;
use Webkul\Inventory\Enums\ProcureMethod;
use Webkul\Inventory\Enums\ProductTracking;
use Webkul\Inventory\Enums\RuleAction;
use Webkul\Inventory\Facades\Inventory as InventoryFacade;
use Webkul\Inventory\Support\StockLevels;
use Webkul\Inventory\Support\StockQueryScopes;
use Webkul\Inventory\Support\StockScope;
use Webkul\Product\Models\Product as BaseProduct;
use Webkul\Security\Models\User;
use Webkul\Support\Services\CompanyContext;

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

    protected ?StockScope $stockScope = null;

    public function withStockScope(StockScope $scope): static
    {
        $this->stockScope = $scope;

        return $this;
    }

    public function stockScope(): StockScope
    {
        return $this->stockScope ??= StockScope::make();
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
        return $this->stockLevels()->onHand;
    }

    public function getFreeQtyAttribute(): float
    {
        return $this->stockLevels()->free;
    }

    public function getIncomingQtyAttribute(): float
    {
        return $this->stockLevels()->incoming;
    }

    public function getOutgoingQtyAttribute(): float
    {
        return $this->stockLevels()->outgoing;
    }

    public function getVirtualAvailableQtyAttribute(): float
    {
        return $this->stockLevels()->forecast;
    }

    public function descriptionFor(OperationType $operationType): ?string
    {
        return match ($operationType->type) {
            OperationTypeEnum::INCOMING => $this->description_pickingin ?? $this->description,
            OperationTypeEnum::OUTGOING => $this->description_pickingout ?? $this->name,
            OperationTypeEnum::INTERNAL => $this->description_picking ?? $this->description,
            default                     => $this->description,
        };
    }

    public function resolveRuleChain($location, $routes = null, ?Collection $seen = null): Collection
    {
        $seen ??= collect();

        $warehouse = $location->warehouse ?? ($seen->isNotEmpty() ? $seen->last()?->propagateWarehouse : null);

        $rule = InventoryFacade::findRule($this, $location, [
            'routes'    => $routes ?: collect(),
            'warehouse' => $warehouse,
        ]);

        if (! $rule) {
            return $seen;
        }

        if ($seen->contains(fn ($seenRule) => $seenRule->id === $rule->id)) {
            throw new \Exception(__('inventories::system.product.endless-loop-rule', ['name' => $rule->name]));
        }

        $seen->push($rule);

        if (
            $rule->procure_method === ProcureMethod::MAKE_TO_STOCK
            || ! in_array($rule->action, [RuleAction::PULL_PUSH, RuleAction::PULL], true)
        ) {
            return $seen;
        }

        return $this->resolveRuleChain($rule->sourceLocation, null, $seen);
    }

    public function procurementDates($date, $location, $routes = null): array
    {
        $rules = $this->resolveRuleChain($location, $routes);

        [$delays] = $rules->getLeadDays($this);

        $leadDays = $delays['total_delay'] ?? 0;

        return [
            'planned'    => Carbon::parse($date)->subDays($leadDays),
            'ordered_at' => Carbon::parse($date)->subDays($leadDays),
        ];
    }

    public function stockLevels(): StockLevels
    {
        if ($this->is_configurable) {
            return $this->variants
                ->reduce(
                    fn (StockLevels $totals, self $variant) => $totals->plus(
                        $variant->withStockScope($this->stockScope())->stockLevels()
                    ),
                    StockLevels::empty()
                )
                ->rounded($this->uom->rounding);
        }

        $scope = $this->stockScope();

        $scopes = $this->resolveStockScopes();

        ['quantity' => $stored, 'reserved' => $reserved] = $this->storedQuantities($scopes, $scope);

        $onHand = $scope->isBackdated()
            ? $stored
                - $this->settledMovesSince($scopes, $scope, incoming: true)
                + $this->settledMovesSince($scopes, $scope, incoming: false)
            : $stored;

        $rounding = $this->uom->rounding;

        $incoming = float_round($this->pendingMoveQuantity($scopes, $scope, incoming: true), precisionRounding: $rounding);

        $outgoing = float_round($this->pendingMoveQuantity($scopes, $scope, incoming: false), precisionRounding: $rounding);

        return new StockLevels(
            onHand: float_round($onHand, precisionRounding: $rounding),
            free: float_round($onHand - $reserved, precisionRounding: $rounding),
            incoming: $incoming,
            outgoing: $outgoing,
            forecast: float_round($onHand + $incoming - $outgoing, precisionRounding: $rounding),
        );
    }

    protected function storedQuantities(StockQueryScopes $scopes, StockScope $scope): array
    {
        $row = ProductQuantity::query()
            ->where('product_id', $this->id)
            ->where(fn (Builder $query) => $scopes->quantities($query))
            ->when($scope->lotId() !== null, fn ($query) => $query->where('lot_id', $scope->lotId()))
            ->when($scope->packageId() !== null, fn ($query) => $query->where('package_id', $scope->packageId()))
            ->selectRaw('SUM(quantity) as quantity, SUM(reserved_quantity) as reserved_quantity')
            ->first();

        return [
            'quantity' => (float) ($row->quantity ?? 0.0),
            'reserved' => (float) ($row->reserved_quantity ?? 0.0),
        ];
    }

    protected function pendingMoveQuantity(StockQueryScopes $scopes, StockScope $scope, bool $incoming): float
    {
        return (float) (Move::query()
            ->where('product_id', $this->id)
            ->whereIn('state', [MoveState::WAITING, MoveState::CONFIRMED, MoveState::ASSIGNED, MoveState::PARTIALLY_ASSIGNED])
            ->where(fn (Builder $query) => $incoming ? $scopes->incomingMoves($query) : $scopes->outgoingMoves($query))
            ->when($scope->fromDate(), fn ($query, $from) => $query->where('scheduled_at', '>=', $from))
            ->when($scope->untilDate(), fn ($query, $until) => $query->where('scheduled_at', '<=', $until))
            ->groupBy('product_id')
            ->selectRaw('product_id, SUM(product_qty) as total')
            ->value('total') ?? 0.0);
    }

    protected function settledMovesSince(StockQueryScopes $scopes, StockScope $scope, bool $incoming): float
    {
        return Move::query()
            ->where('product_id', $this->id)
            ->where('state', MoveState::DONE)
            ->where('scheduled_at', '>', $scope->untilDate())
            ->where(fn (Builder $query) => $incoming ? $scopes->incomingMoves($query) : $scopes->outgoingMoves($query))
            ->groupBy('product_id', 'uom_id')
            ->selectRaw('uom_id, SUM(quantity) as total')
            ->get()
            ->sum(fn ($row) => $row->uom->computeQuantity($row->total, $this->uom));
    }

    public function resolveStockScopes(): StockQueryScopes
    {
        $scope = $this->stockScope();

        $companyIds = $scope->companyIds()
            ?: app(CompanyContext::class)->activeIds()
            ?: array_filter([current_company_id()]);

        return $this->buildStockScopes(
            $this->resolveScopedLocationIds($scope, $companyIds),
            $scope->isStrict()
        );
    }

    protected function resolveScopedLocationIds(StockScope $scope, array $companyIds): array
    {
        if ($scope->warehouseIds() === []) {
            if ($scope->locationIds() !== []) {
                return $scope->locationIds();
            }

            return Warehouse::query()
                ->whereIn('company_id', $companyIds)
                ->pluck('view_location_id')
                ->filter()
                ->unique()
                ->values()
                ->all();
        }

        $warehouseRootIds = Warehouse::query()
            ->whereIn('id', $scope->warehouseIds())
            ->pluck('view_location_id')
            ->filter()
            ->unique()
            ->values()
            ->all();

        if ($scope->locationIds() === []) {
            return $warehouseRootIds;
        }

        $rootPaths = Location::query()
            ->whereIn('id', $warehouseRootIds)
            ->pluck('parent_path')
            ->filter()
            ->all();

        return Location::query()
            ->whereIn('id', $scope->locationIds())
            ->get(['id', 'parent_path'])
            ->filter(fn (Location $location) => $location->parent_path
                && collect($rootPaths)->contains(fn ($path) => str_starts_with($location->parent_path, $path)))
            ->pluck('id')
            ->unique()
            ->values()
            ->all();
    }

    protected function buildStockScopes(array $locationIds, bool $strict = false): StockQueryScopes
    {
        if ($locationIds === []) {
            return StockQueryScopes::matchingNothing();
        }

        $locations = Location::query()
            ->whereIn('id', $locationIds)
            ->get(['id', 'parent_path']);

        if ($locations->isEmpty()) {
            return StockQueryScopes::matchingNothing();
        }

        if ($strict) {
            $ids = $locations->pluck('id')->all();

            return $this->composeStockScopes(
                fn (Builder $query) => $query->whereIn('location_id', $ids),
                fn (Builder $query) => $query->whereIn('source_location_id', $ids),
                fn (Builder $query) => $query->whereIn('destination_location_id', $ids),
            );
        }

        $paths = $locations->pluck('parent_path')->filter()->all();

        if ($paths === []) {
            return StockQueryScopes::matchingNothing();
        }

        $ids = Location::query()
            ->where(function (Builder $query) use ($paths) {
                foreach ($paths as $path) {
                    $query->orWhere('parent_path', 'like', $path.'%');
                }
            })
            ->pluck('id')
            ->all();

        return $this->composeStockScopes(
            fn (Builder $query) => $query->whereIn('location_id', $ids),
            fn (Builder $query) => $query->whereIn('source_location_id', $ids),
            fn (Builder $query) => $query->where(function (Builder $nested) use ($ids) {
                $nested
                    ->where(fn (Builder $withFinal) => $withFinal->whereNotNull('final_location_id')->whereIn('final_location_id', $ids))
                    ->orWhere(fn (Builder $withoutFinal) => $withoutFinal->whereNull('final_location_id')->whereIn('destination_location_id', $ids));
            }),
        );
    }

    protected function composeStockScopes(Closure $stored, Closure $sourced, Closure $delivered): StockQueryScopes
    {
        return new StockQueryScopes(
            $stored,
            fn (Builder $query) => $query
                ->where(fn (Builder $nested) => $delivered($nested))
                ->whereNot(fn (Builder $nested) => $sourced($nested)),
            fn (Builder $query) => $query
                ->where(fn (Builder $nested) => $sourced($nested))
                ->whereNot(fn (Builder $nested) => $delivered($nested)),
        );
    }

    protected static function newFactory(): ProductFactory
    {
        return ProductFactory::new();
    }
}
