<?php

namespace Webkul\Support\Filament\Summarizers;

use Illuminate\Support\Collection;

class Range extends Summarizer
{
    protected bool $minOnly = false;

    protected bool $maxOnly = false;

    protected function setUp(): void
    {
        parent::setUp();

        $this->numeric();
    }

    public function minOnly(bool $condition = true): static
    {
        $this->minOnly = $condition;

        $this->maxOnly = false;

        return $this;
    }

    public function maxOnly(bool $condition = true): static
    {
        $this->maxOnly = $condition;

        $this->minOnly = false;

        return $this;
    }

    protected function calculateSummary(Collection $items, string $columnName): ?string
    {
        if ($items->isEmpty()) {
            return null;
        }

        $values = $items->pluck($columnName)->filter(fn ($value) => ! is_null($value));

        if ($values->isEmpty()) {
            return null;
        }

        $min = $values->min();

        $max = $values->max();

        if ($this->minOnly) {
            return (string) $min;
        }

        if ($this->maxOnly) {
            return (string) $max;
        }

        return "{$min} - {$max}";
    }

    public function getDefaultLabel(): ?string
    {
        if ($this->minOnly) {
            return __('filament-tables::table.summary.summarizers.range.label.min');
        }

        if ($this->maxOnly) {
            return __('filament-tables::table.summary.summarizers.range.label.max');
        }

        return __('filament-tables::table.summary.summarizers.range.label.range');
    }
}
