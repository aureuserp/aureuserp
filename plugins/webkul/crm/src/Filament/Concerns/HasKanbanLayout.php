<?php

namespace Webkul\Crm\Filament\Concerns;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Webkul\Crm\Enums\ViewLayout;
use Wezlo\FilamentKanban\KanbanBoard;

trait HasKanbanLayout
{
    protected bool $appliesKanbanTableView = true;

    abstract protected function modifyKanbanQuery(Builder $query): Builder;

    public function content(Schema $schema): Schema
    {
        return parent::content($schema);
    }

    protected function configureKanbanLayoutTable(Table $table): Table
    {
        $table = $table
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

    public function isKanbanCardFieldVisible(string $column): bool
    {
        return ! $this->getTable()->getColumn($column)?->isToggledHidden();
    }

    public function getTableRecords(): Collection|Paginator|CursorPaginator
    {
        return $this->isViewLayout(ViewLayout::LIST) ? parent::getTableRecords() : new Collection;
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
        if (! $this->isViewLayout(ViewLayout::KANBAN)) {
            return $this->getTableQuery();
        }

        $this->appliesKanbanTableView = false;

        try {
            return $this->getKanbanQuery($this->getKanbanBoard());
        } finally {
            $this->appliesKanbanTableView = true;
        }
    }
}
