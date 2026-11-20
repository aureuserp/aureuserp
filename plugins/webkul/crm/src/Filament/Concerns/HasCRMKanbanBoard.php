<?php

namespace Webkul\Crm\Filament\Concerns;

use Wezlo\FilamentKanban\Concerns\HasKanbanBoard;
use Wezlo\FilamentKanban\KanbanBoard;

trait HasCRMKanbanBoard
{
    use HasKanbanBoard;

    protected ?KanbanBoard $cachedKanbanBoard = null;

    public function getKanbanBoard(): KanbanBoard
    {
        return $this->cachedKanbanBoard ??= $this->kanban(KanbanBoard::make());
    }

    public function updatedHasCRMKanbanBoard(): void
    {
        $this->cachedKanbanBoard = null;
    }

    public function renderingHasCRMKanbanBoard(): void
    {
        $this->cachedKanbanBoard = null;
    }
}
