<?php

namespace Webkul\Crm\Filament\Concerns;

use Filament\Navigation\NavigationItem;

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

    public static function getReportViews(): array
    {
        return [
            'index'   => ['heroicon-o-chart-bar', 'graph', __('crm::filament/clusters/reporting/report.views.graph')],
            'summary' => ['heroicon-o-table-cells', 'summary', __('crm::filament/clusters/reporting/report.views.summary')],
            'list'    => ['heroicon-o-list-bullet', 'list', __('crm::filament/clusters/reporting/report.views.list')],
        ];
    }

    public static function getViewTabs(string $current): array
    {
        return collect(static::getReportViews())
            ->map(fn (array $view, string $page): NavigationItem => NavigationItem::make($view[1])
                ->label($view[2])
                ->icon($view[0])
                ->url(static::getUrl($page))
                ->isActiveWhen(fn (): bool => $page === $current))
            ->values()
            ->all();
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
