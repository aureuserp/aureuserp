<?php

namespace Webkul\Product\Support;

use Illuminate\Database\Eloquent\Model;
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
        foreach (static::$models as $model) {
            $query = $model::query()->withoutGlobalScopes();

            if (! static::tableExists($query->getModel())) {
                continue;
            }

            if ($query->where('product_id', $productId)->exists()) {
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
