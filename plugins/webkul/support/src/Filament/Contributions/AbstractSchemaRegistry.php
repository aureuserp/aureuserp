<?php

namespace Webkul\Support\Filament\Contributions;

use Closure;

abstract class AbstractSchemaRegistry
{
    abstract protected static function scope(): string;

    public static function form(string $slot, Closure $factory, int $priority = 0): void
    {
        SchemaRegistry::form(static::scope(), $slot, $factory, $priority);
    }

    public static function infolist(string $slot, Closure $factory, int $priority = 0): void
    {
        SchemaRegistry::infolist(static::scope(), $slot, $factory, $priority);
    }

    public static function table(string $slot, Closure $factory, int $priority = 0): void
    {
        SchemaRegistry::table(static::scope(), $slot, $factory, $priority);
    }

    public static function actions(string $slot, Closure $factory, int $priority = 0): void
    {
        SchemaRegistry::actions(static::scope(), $slot, $factory, $priority);
    }

    public static function eagerLoad(array $relations): void
    {
        SchemaRegistry::eagerLoad(static::scope(), $relations);
    }

    public static function eagerLoads(): array
    {
        return SchemaRegistry::eagerLoads(static::scope());
    }

    public static function hasFormSlot(string $slot): bool
    {
        return SchemaRegistry::hasForm(static::scope(), $slot);
    }

    public static function hasInfolistSlot(string $slot): bool
    {
        return SchemaRegistry::hasInfolist(static::scope(), $slot);
    }

    public static function hasTableSlot(string $slot): bool
    {
        return SchemaRegistry::hasTable(static::scope(), $slot);
    }

    public static function hasActionSlot(string $slot): bool
    {
        return SchemaRegistry::hasActions(static::scope(), $slot);
    }

    public static function renderForm(string $slot, mixed ...$args): array
    {
        return SchemaRegistry::renderForm(static::scope(), $slot, ...$args);
    }

    public static function renderInfolist(string $slot, mixed ...$args): array
    {
        return SchemaRegistry::renderInfolist(static::scope(), $slot, ...$args);
    }

    public static function renderTable(string $slot, mixed ...$args): array
    {
        return SchemaRegistry::renderTable(static::scope(), $slot, ...$args);
    }

    public static function renderActions(string $slot, mixed ...$args): array
    {
        return SchemaRegistry::renderActions(static::scope(), $slot, ...$args);
    }
}
