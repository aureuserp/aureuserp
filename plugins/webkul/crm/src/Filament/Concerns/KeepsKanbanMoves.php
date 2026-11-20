<?php

namespace Webkul\Crm\Filament\Concerns;

use Filament\Support\Livewire\Partials\PartialsComponentHook;
use Wezlo\FilamentKanban\KanbanColumn;

trait KeepsKanbanMoves
{
    protected ?string $movedKanbanCardId = null;

    protected int $keptKanbanMoves = 0;

    protected array $keptKanbanColumns = [];

    protected array $kanbanDropOrders = [];

    public function moveKanbanRecords(array $moves): void
    {
        foreach (array_slice($moves, 0, 100) as $move) {
            [$recordId, $column, $orderedIds] = array_pad(array_values((array) $move), 3, null);

            if (! is_scalar($recordId)
                || ! is_scalar($column)
                || ! is_array($orderedIds)) {
                continue;
            }

            $keptMoves = $this->keptKanbanMoves;

            $this->moveRecord((string) $recordId, (string) $column, array_values(array_map('strval', array_filter($orderedIds, 'is_scalar'))));

            if ($this->keptKanbanMoves === $keptMoves) {
                app(PartialsComponentHook::class)->forceRender($this);
            }
        }
    }

    protected function keepKanbanMove(string $recordId, string $from, string $to, array $orderedIds): void
    {
        $this->keptKanbanMoves++;

        if ($from !== $to) {
            $this->movedKanbanCardId = $recordId;
        }

        if (isset($this->kanbanDropOrders[$from])) {
            $this->kanbanDropOrders[$from] = array_values(array_diff($this->kanbanDropOrders[$from], [$recordId]));
        }

        $this->kanbanDropOrders[$to] = array_map('strval', $orderedIds);

        if ($this->keptKanbanColumns === []) {
            app(PartialsComponentHook::class)->renderPartial($this, fn (): array => $this->renderKeptKanbanColumns());
        }

        $this->keptKanbanColumns = array_values(array_unique([...$this->keptKanbanColumns, $from, $to]));
    }

    protected function renderKeptKanbanColumns(): array
    {
        $columns = collect($this->getKanbanColumns())->keyBy(fn (KanbanColumn $column): string => (string) $column->value);

        return collect($this->keptKanbanColumns)
            ->filter(fn (string $value): bool => $columns->has($value))
            ->mapWithKeys(fn (string $value): array => ["kanban-column-{$value}" => view($this->getKanbanBoard()->getBoardView(), [
                'kanbanColumn' => $columns[$value],
                'dropOrder'    => $this->kanbanDropOrders[$value] ?? null,
            ])])
            ->all();
    }
}
