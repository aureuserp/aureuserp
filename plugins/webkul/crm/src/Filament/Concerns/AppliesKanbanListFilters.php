<?php

namespace Webkul\Crm\Filament\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Wezlo\FilamentKanban\KanbanBoard;

trait AppliesKanbanListFilters
{
    abstract protected function kanbanSimpleFilterKeys(): array;

    abstract protected function applyKanbanListFilters(Builder $query, array $filters): Builder;

    protected function getKanbanQuery(KanbanBoard $board): Builder
    {
        $filters = $this->kanbanFilters;

        $this->kanbanFilters = array_intersect_key($filters, array_flip($this->kanbanSimpleFilterKeys()));

        try {
            $query = $this->getBaseKanbanQuery($board);
        } finally {
            $this->kanbanFilters = $filters;
        }

        return $this->applyKanbanListFilters($query, $filters);
    }

    protected static function kanbanFilterValues(array $filters, string $key): array
    {
        return array_values(array_filter((array) ($filters[$key] ?? []), fn (mixed $value): bool => filled($value)));
    }
}
