<?php

namespace Webkul\Crm\Filament\Concerns;

use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Webkul\TableViews\Filament\Components\PresetView;
use Wezlo\FilamentKanban\KanbanBoard;

trait HasKanbanTableViews
{
    use HasCRMTableViews;

    protected bool $appliesKanbanTableView = true;

    abstract protected function getTableViewsListPage(): ListRecords;

    abstract protected function modifyKanbanQuery(Builder $query): Builder;

    public function content(Schema $schema): Schema
    {
        return parent::content($schema);
    }

    public function table(Table $table): Table
    {
        $table = $this->configureKanbanBaseTable($table)
            ->paginated(false)
            ->groups([])
            ->toolbarActions([])
            ->emptyState(fn () => view($this->getKanbanBoard()->getBoardView()));

        foreach ($table->getColumns() as $column) {
            if (! $column->isToggleable()) {
                $column->toggleable();
            }
        }

        return $table;
    }

    protected function configureKanbanBaseTable(Table $table): Table
    {
        return parent::table($table);
    }

    public function isKanbanCardFieldVisible(string $column): bool
    {
        return ! $this->getTable()->getColumn($column)?->isToggledHidden();
    }

    public function getTableRecords(): Collection|Paginator|CursorPaginator
    {
        return new Collection;
    }

    public function getPresetTableViews(): array
    {
        return array_map(
            fn (PresetView $view): PresetView => $view->setAsDefault(false),
            $this->getTableViewsListPage()->getPresetTableViews(),
        );
    }

    public function getSavedTableViews(): array
    {
        return $this->getTableViewsListPage()->getSavedTableViews();
    }

    protected function getTableViewsFilterableType(): string
    {
        return $this->getTableViewsListPage()::class;
    }

    protected function getKanbanQuery(KanbanBoard $board): Builder
    {
        $query = static::getResource()::getEloquentQuery();

        if ($modifier = $board->getQueryModifier()) {
            $query = $modifier($query);
        }

        $query = $this->modifyKanbanQuery($query);

        if (! $this->appliesKanbanTableView) {
            return $query;
        }

        return $this->filterTableQuery($this->modifyQueryWithActiveTab($query));
    }

    protected function getTableViewCountQuery(): Builder
    {
        $this->appliesKanbanTableView = false;

        try {
            return $this->getKanbanQuery($this->getKanbanBoard());
        } finally {
            $this->appliesKanbanTableView = true;
        }
    }
}
