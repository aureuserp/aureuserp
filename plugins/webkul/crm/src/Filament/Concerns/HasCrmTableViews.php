<?php

namespace Webkul\Crm\Filament\Concerns;

use Filament\Actions\Action;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Webkul\TableViews\Filament\Components\PresetView;
use Webkul\TableViews\Filament\Components\SavedView;
use Webkul\TableViews\Filament\Concerns\HasTableViews;

trait HasCrmTableViews
{
    use HasTableViews {
        HasTableViews::createTableViewAction as getBaseCreateTableViewAction;
        HasTableViews::getCachedFavoriteTableViews as getBaseCachedFavoriteTableViews;
    }

    protected array $tableViewCounts = [];

    public function createTableViewAction(): Action
    {
        return $this->getBaseCreateTableViewAction()
            ->fillForm(['is_favorite' => true])
            ->mutateDataUsing(fn (array $data): array => [
                ...$data,
                'user_id'         => Auth::id(),
                'filterable_type' => $this->getTableViewsFilterableType(),
                'filters'         => [
                    'tableFilters'        => $this->tableFilters,
                    'tableGrouping'       => $this->tableGrouping,
                    'tableSearch'         => $this->tableSearch,
                    'tableColumnSearches' => $this->tableColumnSearches,
                    'tableSort'           => $this->tableSort,
                    'tableRecordsPerPage' => $this->tableRecordsPerPage,
                ],
            ]);
    }

    protected function getTableViewsFilterableType(): string
    {
        return static::class;
    }

    public function getCachedFavoriteTableViews(): array
    {
        $views = $this->getBaseCachedFavoriteTableViews();

        foreach ($views as $key => $view) {
            $view->badge(fn (): int => $this->tableViewCounts[$key] ??= $this->countTableViewRecords($view));
        }

        return $views;
    }

    protected function getTableViewCountQuery(): Builder
    {
        return $this->getTableQuery();
    }

    protected function countTableViewRecords(PresetView $view): int
    {
        $query = $view->modifyQuery($this->getTableViewCountQuery());

        if (! $view instanceof SavedView) {
            return $query->count();
        }

        $currentFilters = $this->tableFilters;

        try {
            $this->tableFilters = $view->getRecord()->filters['tableFilters'] ?? [];

            return $this->applyFiltersToTableQuery($query)->count();
        } finally {
            $this->tableFilters = $currentFilters;
        }
    }
}
