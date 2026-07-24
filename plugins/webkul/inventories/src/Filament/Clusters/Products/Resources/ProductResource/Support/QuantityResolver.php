<?php

namespace Webkul\Inventory\Filament\Clusters\Products\Resources\ProductResource\Support;

use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Webkul\Inventory\Enums\MoveState;
use Webkul\Inventory\Models\Move;
use Webkul\Inventory\Models\Product;
use Webkul\Inventory\Models\ProductQuantity;

class QuantityResolver
{
    /**
     * @var array<int, array<string, float>>
     */
    protected array $quantities = [];

    public function onHand(Model $record, mixed $scope = null): float
    {
        return $this->get($record, 'on_hand', $scope);
    }

    public function forecasted(Model $record, mixed $scope = null): float
    {
        return $this->get($record, 'forecasted', $scope);
    }

    protected function get(Model $record, string $key, mixed $scope): float
    {
        $id = $record->getKey();

        if (! array_key_exists($id, $this->quantities)) {
            $this->load($this->products($record, $scope));
        }

        return $this->quantities[$id][$key] ?? 0.0;
    }

    /**
     * @param  Collection<int, Model>  $products
     */
    protected function load(Collection $products): void
    {
        $ids = $products
            ->map(fn (Model $product): int => $product->getKey())
            ->all();

        $variants = Product::query()
            ->whereIn('parent_id', $ids)
            ->get(['id', 'parent_id']);

        $allIds = array_merge($ids, $variants->pluck('id')->all());

        $onHand = $this->sumQuantities($allIds);
        $incoming = $this->sumMoves($allIds, incoming: true);
        $outgoing = $this->sumMoves($allIds, incoming: false);

        $variantsByParent = $variants->groupBy('parent_id');

        foreach ($products as $product) {
            $id = $product->getKey();

            $sourceIds = $product->is_configurable
                ? $variantsByParent->get($id, collect())->map(fn (Product $variant): int => $variant->getKey())->all()
                : [$id];

            $available = 0.0;
            $in = 0.0;
            $out = 0.0;

            foreach ($sourceIds as $sourceId) {
                $available += (float) ($onHand[$sourceId] ?? 0);
                $in += (float) ($incoming[$sourceId] ?? 0);
                $out += (float) ($outgoing[$sourceId] ?? 0);
            }

            $rounding = $product->uom?->rounding;

            $this->quantities[$id] = [
                'on_hand'    => $this->round($available, $rounding),
                'forecasted' => $this->round($available + $in - $out, $rounding),
            ];
        }
    }

    protected function round(float $quantity, ?float $rounding): float
    {
        return $rounding
            ? float_round($quantity, precisionRounding: $rounding)
            : $quantity;
    }

    /**
     * @param  array<int, int>  $productIds
     * @return array<int, float>
     */
    protected function sumQuantities(array $productIds): array
    {
        if ($productIds === []) {
            return [];
        }

        [$quantityScope] = (new Product)->getLocationFilters();

        return ProductQuantity::query()
            ->whereIn('product_id', $productIds)
            ->where(fn (Builder $query) => $quantityScope($query))
            ->groupBy('product_id')
            ->selectRaw('product_id, SUM(quantity) as total')
            ->pluck('total', 'product_id')
            ->all();
    }

    /**
     * @param  array<int, int>  $productIds
     * @return array<int, float>
     */
    protected function sumMoves(array $productIds, bool $incoming): array
    {
        if ($productIds === []) {
            return [];
        }

        [, $moveInScope, $moveOutScope] = (new Product)->getLocationFilters();

        $scope = $incoming ? $moveInScope : $moveOutScope;

        return Move::query()
            ->whereIn('product_id', $productIds)
            ->whereIn('state', [MoveState::WAITING, MoveState::CONFIRMED, MoveState::ASSIGNED, MoveState::PARTIALLY_ASSIGNED])
            ->where(fn (Builder $query) => $scope($query))
            ->groupBy('product_id')
            ->selectRaw('product_id, SUM(product_qty) as total')
            ->pluck('total', 'product_id')
            ->all();
    }

    /**
     * @return Collection<int, Model>
     */
    protected function products(Model $record, mixed $scope): Collection
    {
        $products = match (true) {
            $scope instanceof Paginator, $scope instanceof CursorPaginator => collect($scope->items()),
            $scope instanceof Collection                                   => $scope,
            default                                                        => collect(),
        };

        $products = $products
            ->filter(fn (mixed $product): bool => $product instanceof Model)
            ->values();

        if (! $products->contains(fn (Model $product): bool => $product->getKey() === $record->getKey())) {
            $products->push($record);
        }

        return $products;
    }
}
