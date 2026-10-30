<?php

namespace Webkul\Crm\Filament\Concerns;

use Webkul\Crm\Enums\ViewLayout;

trait IsCRMReportResource
{
    use HasCRMAuthorization;

    abstract public static function getReport(): string;

    abstract public static function getReportTitle(): string;

    public static function getModelLabel(): string
    {
        return static::getReportTitle();
    }

    public static function getPluralModelLabel(): string
    {
        return static::getReportTitle();
    }

    public static function getReportLayouts(): array
    {
        return [ViewLayout::GRAPH, ViewLayout::SUMMARY, ViewLayout::LIST];
    }

    public static function getReportTableFilters(): array
    {
        return [
            ...static::getReport()::getTableFilters(),
            ...static::getReportCustomFilters(),
        ];
    }

    public static function getReportCustomFilters(): array
    {
        return [];
    }

    public static function getReportTableViews(): array
    {
        return [];
    }

    public static function getReportBulkActions(): array
    {
        return [];
    }
}
