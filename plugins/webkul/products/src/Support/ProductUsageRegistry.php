<?php

namespace Webkul\Product\Support;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use InvalidArgumentException;

class ProductUsageRegistry
{
    protected static array $models = [];

    protected static array $tableExists = [];

    public static function register(string ...$models): void
    {
        foreach ($models as $model) {
            if (! is_subclass_of($model, Model::class)) {
                throw new InvalidArgumentException(
                    "[{$model}] is not an Eloquent model and cannot be registered as product usage."
                );
            }

            static::$models[$model] = $model;
        }
    }

    public static function models(): array
    {
        return array_values(static::$models);
    }

    public static function isProductInUse(int|string $productId): bool
    {
        return static::isAnyProductInUse([$productId]);
    }

    /**
     * One existence check per registered model rather than one per product, so this stays cheap
     * for a product with many variants.
     *
     * @param  array<int, int|string>|Collection  $productIds
     */
    public static function isAnyProductInUse($productIds): bool
    {
        $productIds = collect($productIds)->all();

        if ($productIds === []) {
            return false;
        }

        foreach (static::$models as $model) {
            $query = $model::query()->withoutGlobalScopes();

            if (! static::tableExists($query->getModel())) {
                continue;
            }

            if ($query->whereIn('product_id', $productIds)->exists()) {
                return true;
            }
        }

        return false;
    }

    protected static function tableExists(Model $model): bool
    {
        $table = $model->getTable();

        return static::$tableExists[$table] ??= $model->getConnection()
            ->getSchemaBuilder()
            ->hasTable($table);
    }

    public static function flush(): void
    {
        static::$models = [];

        static::$tableExists = [];
    }
}
