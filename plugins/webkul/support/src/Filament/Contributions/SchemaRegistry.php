<?php

namespace Webkul\Support\Filament\Contributions;

use Closure;

class SchemaRegistry
{
    protected static array $form = [];

    protected static array $infolist = [];

    protected static array $table = [];

    protected static array $actions = [];

    protected static array $presetViews = [];

    protected static array $eagerLoad = [];

    protected static array $companyDependentFields = [];

    protected static array $companyDefaultFields = [];

    public static function form(string $scope, string $slot, Closure $factory, int $priority = 0): void
    {
        static::$form[$scope][$slot][] = [$priority, $factory];
    }

    public static function infolist(string $scope, string $slot, Closure $factory, int $priority = 0): void
    {
        static::$infolist[$scope][$slot][] = [$priority, $factory];
    }

    public static function table(string $scope, string $slot, Closure $factory, int $priority = 0): void
    {
        static::$table[$scope][$slot][] = [$priority, $factory];
    }

    public static function actions(string $scope, string $slot, Closure $factory, int $priority = 0): void
    {
        static::$actions[$scope][$slot][] = [$priority, $factory];
    }

    public static function presetView(string $scope, string $key, Closure $factory, int $priority = 0): void
    {
        static::$presetViews[$scope][] = [$priority, $key, $factory];
    }

    public static function eagerLoad(string $scope, array $relations): void
    {
        static::$eagerLoad[$scope] = array_values(array_unique([
            ...(static::$eagerLoad[$scope] ?? []),
            ...$relations,
        ]));
    }

    public static function eagerLoads(string $scope): array
    {
        return static::$eagerLoad[$scope] ?? [];
    }

    public static function companyDependentFields(string $scope, array $fields): void
    {
        static::$companyDependentFields[$scope] = [
            ...(static::$companyDependentFields[$scope] ?? []),
            ...$fields,
        ];
    }

    public static function companyDefaultFields(string $scope, array $resolvers): void
    {
        static::$companyDefaultFields[$scope] = array_merge(
            static::$companyDefaultFields[$scope] ?? [],
            $resolvers,
        );
    }

    public static function companyDefaultFieldsFor(string $scope): array
    {
        return static::$companyDefaultFields[$scope] ?? [];
    }

    public static function companyDependentFieldsFor(string $scope): array
    {
        return static::$companyDependentFields[$scope] ?? [];
    }

    public static function hasForm(string $scope, string $slot): bool
    {
        return ! empty(static::$form[$scope][$slot]);
    }

    public static function hasInfolist(string $scope, string $slot): bool
    {
        return ! empty(static::$infolist[$scope][$slot]);
    }

    public static function hasTable(string $scope, string $slot): bool
    {
        return ! empty(static::$table[$scope][$slot]);
    }

    public static function hasActions(string $scope, string $slot): bool
    {
        return ! empty(static::$actions[$scope][$slot]);
    }

    public static function renderForm(string $scope, string $slot, mixed ...$args): array
    {
        return static::resolve(static::$form[$scope][$slot] ?? [], $args);
    }

    public static function renderInfolist(string $scope, string $slot, mixed ...$args): array
    {
        return static::resolve(static::$infolist[$scope][$slot] ?? [], $args);
    }

    public static function renderTable(string $scope, string $slot, mixed ...$args): array
    {
        return static::resolve(static::$table[$scope][$slot] ?? [], $args);
    }

    public static function renderActions(string $scope, string $slot, mixed ...$args): array
    {
        return static::resolve(static::$actions[$scope][$slot] ?? [], $args);
    }

    public static function renderPresetViews(string $scope, mixed ...$args): array
    {
        $entries = static::$presetViews[$scope] ?? [];

        usort($entries, fn (array $a, array $b): int => $a[0] <=> $b[0]);

        $out = [];

        foreach ($entries as [$priority, $key, $factory]) {
            $out[$key] = $factory(...$args);
        }

        return $out;
    }

    protected static function resolve(array $entries, array $args): array
    {
        usort($entries, fn (array $a, array $b): int => $a[0] <=> $b[0]);

        $out = [];

        foreach ($entries as [$priority, $factory]) {
            $result = $factory(...$args);

            if (is_array($result)) {
                $out = array_merge($out, $result);
            } elseif ($result !== null) {
                $out[] = $result;
            }
        }

        return $out;
    }
}
