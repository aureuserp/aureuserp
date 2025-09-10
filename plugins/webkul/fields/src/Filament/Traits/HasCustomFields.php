<?php

namespace Webkul\Field\Filament\Traits;

use Webkul\Field\Filament\Forms\Components\CustomFields;
use Webkul\Field\Filament\Infolists\Components\CustomEntries;
use Webkul\Field\Filament\Tables\Columns\CustomColumns;
use Webkul\Field\Filament\Tables\Filters\CustomFilters;

trait HasCustomFields
{
    public static function mergeCustomFormFields(array $baseSchema, array $include = [], array $exclude = []): array
    {
        return array_merge($baseSchema, static::getCustomFormFields($include, $exclude));
    }

    public static function mergeCustomTableColumns(array $baseColumns, array $include = [], array $exclude = []): array
    {
        return array_merge($baseColumns, static::getCustomTableColumns($include, $exclude));
    }

    public static function mergeCustomTableFilters(array $baseFilters, array $include = [], array $exclude = []): array
    {
        return array_merge($baseFilters, static::getCustomTableFilters($include, $exclude));
    }

    public static function mergeCustomTableQueryBuilderConstraints(array $baseConstraints, array $include = [], array $exclude = []): array
    {
        return array_merge($baseConstraints, static::getTableQueryBuilderConstraints($include, $exclude));
    }

    public static function mergeCustomInfolistEntries(array $baseSchema, array $include = [], array $exclude = []): array
    {
        return array_merge($baseSchema, static::getCustomInfolistEntries($include, $exclude));
    }

    public static function getCustomFormFields(array $include = [], array $exclude = []): array
    {
        return CustomFields::make(static::class)
            ->include($include)
            ->exclude($exclude)
            ->getSchema();
    }

    public static function getCustomTableColumns(array $include = [], array $exclude = []): array
    {
        return CustomColumns::make(static::class)
            ->include($include)
            ->exclude($exclude)
            ->getColumns();
    }

    public static function getCustomTableFilters(array $include = [], array $exclude = []): array
    {
        return CustomFilters::make(static::class)
            ->include($include)
            ->exclude($exclude)
            ->getFilters();
    }

    public static function getTableQueryBuilderConstraints(array $include = [], array $exclude = []): array
    {
        return CustomFilters::make(static::class)
            ->include($include)
            ->exclude($exclude)
            ->getQueryBuilderConstraints();
    }

    public static function getCustomInfolistEntries(array $include = [], array $exclude = []): array
    {
        return CustomEntries::make(static::class)
            ->include($include)
            ->exclude($exclude)
            ->getSchema();
    }
}
