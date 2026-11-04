<?php

namespace Webkul\Crm\Filament\Concerns;

use Filament\Tables\Table;
use Webkul\Crm\Enums\ViewLayout;

trait HasGroupBoardLayout
{
    abstract public function getBoardGroup(): mixed;

    public function needsBoardGroup(): bool
    {
        return $this->isViewLayout(ViewLayout::KANBAN) && blank($this->getBoardGroup());
    }

    public function getDefaultBoardGroup(Table $table): ?string
    {
        return array_key_first($table->getGroups());
    }

    protected function configureGroupBoardTable(Table $table): Table
    {
        return $table
            ->defaultGroup(fn (Table $table): ?string => $this->isViewLayout(ViewLayout::KANBAN) ? $this->getDefaultBoardGroup($table) : null)
            ->paginated(fn (): bool => $this->isViewLayout(ViewLayout::LIST))
            ->emptyStateHeading(fn (): ?string => $this->needsBoardGroup() ? __('crm::filament/kanban.choose-group.heading') : null)
            ->emptyStateDescription(fn (): ?string => $this->needsBoardGroup() ? __('crm::filament/kanban.choose-group.description') : null)
            ->emptyStateIcon(fn (): ?string => $this->needsBoardGroup() ? 'heroicon-o-rectangle-group' : null);
    }
}
