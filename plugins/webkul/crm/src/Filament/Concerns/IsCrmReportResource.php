<?php

namespace Webkul\Crm\Filament\Concerns;

use Closure;
use Filament\Actions\Action;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Crm\Filament\Actions\CrmExportAction;

trait IsCrmReportResource
{
    use HasCrmAuthorization;

    abstract public static function getReport(): string;

    public static function getReportTitle(): string
    {
        return __('crm::filament/clusters/reporting/resources/'.static::getReportKey().'.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('crm::filament/clusters/reporting/resources/'.static::getReportKey().'.navigation.title');
    }

    public static function getModelLabel(): string
    {
        return static::getReportTitle();
    }

    public static function getPluralModelLabel(): string
    {
        return static::getReportTitle();
    }

    abstract public static function getReportKey(): string;

    public static function getReportViews(): array
    {
        return [
            'index'   => ['heroicon-o-chart-bar', 'graph'],
            'summary' => ['heroicon-o-table-cells', 'summary'],
            'list'    => ['heroicon-o-list-bullet', 'list'],
        ];
    }

    public static function getViewSwitcherActions(string $currentPage): array
    {
        return collect(static::getReportViews())
            ->except($currentPage)
            ->map(fn (array $view, string $page): Action => Action::make('view_'.$view[1])
                ->label(__('crm::filament/clusters/reporting/report.views.'.$view[1]))
                ->icon($view[0])
                ->color('gray')
                ->url(fn (): string => static::getUrl($page)))
            ->values()
            ->all();
    }

    public static function getExporter(): ?string
    {
        return null;
    }

    public static function getExportActions(?Closure $limitQuery = null): array
    {
        if (! ($exporter = static::getExporter())) {
            return [];
        }

        $action = CrmExportAction::make()->exporter($exporter);

        if ($limitQuery) {
            $action->modifyQueryUsing(fn (Builder $query): Builder => CrmExportAction::limitToVisibleRecords($limitQuery($query)));
        }

        return [$action];
    }
}
