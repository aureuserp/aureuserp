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

    protected function configureGroupBoardTable(Table $table): Table
    {
        return $table
            ->paginated(fn (): bool => $this->isViewLayout(ViewLayout::LIST))
            ->emptyStateHeading(fn (): ?string => $this->needsBoardGroup() ? __('crm::filament/kanban.choose-group.heading') : null)
            ->emptyStateDescription(fn (): ?string => $this->needsBoardGroup() ? __('crm::filament/kanban.choose-group.description') : null)
            ->emptyStateIcon(fn (): ?string => $this->needsBoardGroup() ? 'heroicon-o-rectangle-group' : null);
    }
}
