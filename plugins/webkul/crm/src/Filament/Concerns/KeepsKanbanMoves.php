<?php

namespace Webkul\Crm\Filament\Concerns;

use Filament\Support\Livewire\Partials\PartialsComponentHook;
use Wezlo\FilamentKanban\KanbanColumn;

trait KeepsKanbanMoves
{
    protected function keepKanbanMove(string $from, string $to, array $orderedIds): void
    {
        app(PartialsComponentHook::class)->renderPartial($this, function () use ($from, $to, $orderedIds): array {
            $columns = collect($this->getKanbanColumns())->keyBy(fn (KanbanColumn $column): string => (string) $column->value);

            return collect(array_unique([$from, $to]))
                ->filter(fn (string $value): bool => $columns->has($value))
                ->mapWithKeys(fn (string $value): array => ["kanban-column-{$value}" => view($this->getKanbanBoard()->getBoardView(), [
                    'kanbanColumn' => $columns[$value],
                    'dropOrder'    => $value === $to ? array_map('strval', $orderedIds) : null,
                ])])
                ->all();
        });
    }
}
