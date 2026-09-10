<?php

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class CompanyScopeHelper
{
    /**
     * Every model of the given plugin that opts into company scoping.
     *
     * @return array<int, class-string<Model>>
     */
    public static function companyModels(string $plugin): array
    {
        $models = [];

        foreach (glob(base_path("plugins/webkul/{$plugin}/src/Models/*.php")) as $file) {
            $source = file_get_contents($file);

            if (! str_contains($source, 'use BelongsToCompany;')) {
                continue;
            }

            if (! preg_match('/^namespace ([^;]+);/m', $source, $namespace)) {
                continue;
            }

            if (! preg_match('/^(?:final )?class (\w+)/m', $source, $class)) {
                continue;
            }

            $model = $namespace[1].'\\'.$class[1];

            if (! class_exists($model) || ! is_subclass_of($model, Model::class)) {
                continue;
            }

            $models[] = $model;
        }

        sort($models);

        return $models;
    }

    public static function autoAssignsCompany(string $model): bool
    {
        return (new ReflectionMethod($model, 'autoAssignsCompany'))->invoke(null);
    }

    /**
     * Models of the plugin that stamp the active company but are not declared shared.
     *
     * @param  array<int, class-string<Model>>  $shared
     * @return array<int, class-string<Model>>
     */
    public static function unexpectedlyShared(string $plugin, array $shared): array
    {
        return collect(static::companyModels($plugin))
            ->reject(fn (string $model) => in_array($model, $shared, true))
            ->reject(fn (string $model) => static::autoAssignsCompany($model))
            ->values()
            ->all();
    }

    /**
     * Declared shared models of the plugin that stamp the active company anyway.
     *
     * @param  array<int, class-string<Model>>  $shared
     * @return array<int, class-string<Model>>
     */
    public static function unexpectedlyScoped(array $shared): array
    {
        return collect($shared)
            ->filter(fn (string $model) => static::autoAssignsCompany($model))
            ->values()
            ->all();
    }

    /**
     * Declared shared models whose company column cannot hold null.
     *
     * @param  array<int, class-string<Model>>  $shared
     * @return array<int, class-string<Model>>
     */
    public static function withNonNullableCompanyColumn(array $shared): array
    {
        $offenders = [];

        foreach ($shared as $model) {
            $table = (new $model)->getTable();

            if (! Schema::hasTable($table)) {
                continue;
            }

            $column = collect(Schema::getColumns($table))->firstWhere('name', 'company_id');

            if ($column && ! $column['nullable']) {
                $offenders[] = $model;
            }
        }

        return $offenders;
    }
}
