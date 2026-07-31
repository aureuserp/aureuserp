<?php

namespace Webkul\Inventory\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Webkul\Inventory\Enums\RuleAction;
use Webkul\Inventory\Models\Location;
use Webkul\Inventory\Models\Product;
use Webkul\Inventory\Models\Rule;
use Webkul\Inventory\Models\Warehouse;

class RuleResolver
{
    public function findRule(Product $product, Location $location, array $options = []): ?Rule
    {
        return $this->climbFrom($location, $product, $options, 'destination_location_id', [
            'action' => ['!=', RuleAction::PUSH],
        ]);
    }

    public function findPushRule(Product $product, Location $destination, array $options = []): ?Rule
    {
        return $this->climbFrom($destination, $product, $options, 'source_location_id', [
            'action' => [RuleAction::PUSH, RuleAction::PULL_PUSH],
        ]);
    }

    public function matchRule(?Collection $routes, $packaging, ?Product $product, ?Warehouse $warehouse, array $filters): ?Rule
    {
        if ($warehouse) {
            $filters['warehouse_id'] = $warehouse->id;
        }

        $routeIds = $routes?->pluck('id') ?? collect();

        foreach ([$packaging, $product, $product?->category, $warehouse] as $source) {
            if (! $source || ! $source->routes) {
                continue;
            }

            $routeIds = $routeIds->merge($source->routes->pluck('id'))->unique();

            if ($routeIds->isEmpty()) {
                continue;
            }

            $rule = Rule::query()
                ->whereIn('route_id', $routeIds)
                ->where(fn (Builder $query) => $this->applyFilters($query, $filters))
                ->orderBy('route_sort')
                ->orderBy('sort')
                ->first();

            if ($rule) {
                return $rule;
            }
        }

        return null;
    }

    protected function climbFrom(?Location $location, Product $product, array $options, string $column, array $filters): ?Rule
    {
        while ($location) {
            $filters[$column] = $location->id;

            $rule = $this->matchRule(
                $options['routes'] ?? collect(),
                $options['packaging'] ?? null,
                $product,
                $options['warehouse'] ?? null,
                $filters
            );

            if ($rule) {
                return $rule;
            }

            $location = $location->parent;
        }

        return null;
    }

    protected function applyFilters(Builder $query, array $filters): void
    {
        foreach ($filters as $column => $value) {
            if (! is_array($value)) {
                $query->where($column, $value);

                continue;
            }

            if (count($value) === 2 && $value[0] === '!=') {
                $query->where($column, '!=', $value[1]);

                continue;
            }

            $query->whereIn($column, $value);
        }
    }
}
