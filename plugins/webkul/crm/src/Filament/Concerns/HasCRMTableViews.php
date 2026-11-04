<?php

namespace Webkul\Crm\Filament\Concerns;

use Filament\Actions\Action;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Webkul\TableViews\Filament\Components\PresetView;
use Webkul\TableViews\Filament\Components\SavedView;
use Webkul\TableViews\Filament\Concerns\HasTableViews;

trait HasCRMTableViews
{
    use HasTableViews {
        HasTableViews::createTableViewAction as getBaseCreateTableViewAction;
        HasTableViews::getCachedFavoriteTableViews as getBaseCachedFavoriteTableViews;
        HasTableViews::resetTableGrouping as resetBaseTableGrouping;
        HasTableViews::loadView as loadBaseView;
    }

    protected array $tableViewCounts = [];

    public function resetTableGrouping(): void
    {
        $this->resetBaseTableGrouping();

        $table = $this->getTable();

        if ($table->isDefaultGroupSelectable()) {
            $this->tableGrouping = $table->getDefaultGroup()->getId().':'.$table->getDefaultGroupDirection();
        }
    }

    public function loadView($tabKey): void
    {
        if (($this->getAllTableViews()[$tabKey] ?? null) instanceof SavedView) {
            $this->loadBaseView($tabKey);

            return;
        }

        $filters = $this->tableFilters;

        $search = $this->tableSearch;

        $this->loadBaseView($tabKey);

        $this->getTableFiltersForm()->fill($filters);

        if ($this->getTable()->hasDeferredFilters()) {
            $this->tableFilters = $this->tableDeferredFilters;
        }

        $this->tableSearch = $search;
    }

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
            return $this->applySearchToTableQuery($this->applyFiltersToTableQuery($query))->count();
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
