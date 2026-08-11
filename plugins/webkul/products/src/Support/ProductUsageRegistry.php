<?php

namespace Webkul\Product\Support;

use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

/**
 * Models whose rows mean a product has been used on a real document.
 *
 * Each plugin registers its own models from its service provider, behind an
 * install guard, so an uninstalled plugin costs nothing here: it never
 * registers, and no query is ever run against its tables.
 *
 * Configuration that simply travels with the product — tags, taxes, routes,
 * pricelists, reordering rules — is deliberately absent: none of it breaks when
 * the product turns into a configurable parent. Only register a model when a
 * row in its table would be corrupted by that transition.
 */
class ProductUsageRegistry
{
    /**
     * Registered models, keyed by class name so repeated registration is a no-op.
     *
     * @var array<class-string<Model>, class-string<Model>>
     */
    protected static array $models = [];

    /**
     * Memoised table-existence checks, keyed by table name.
     *
     * @var array<string, bool>
     */
    protected static array $tableExists = [];

    /**
     * Register one or more models as evidence of product usage.
     *
     * @param  class-string<Model>  ...$models
     */
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

    /**
     * @return array<int, class-string<Model>>
     */
    public static function models(): array
    {
        return array_values(static::$models);
    }

    /**
     * Determine whether any registered model references the given product.
     *
     * Global scopes are stripped on purpose. Usage is company-blind: a product
     * sitting on another company's documents is still in use, even though the
     * current user cannot see those rows.
     */
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

    /**
     * Installed state is a boot-time snapshot (see Package::isPluginInstalled), so a
     * registered model can outlive its table — a plugin uninstalled after boot, or a
     * partially migrated database. The check is memoised because tables cannot appear
     * or disappear midway through a request.
     */
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
