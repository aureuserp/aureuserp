<?php

namespace Webkul\Support\Filament\Summarizers;

use Illuminate\Support\Collection;

class Sum extends Summarizer
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->numeric();
    }

    protected function calculateSummary(Collection $items, string $columnName): int|float|null
    {
        return $items->sum(fn ($item) => (float) $item[$columnName]);
    }

    public function getDefaultLabel(): ?string
    {
        return __('filament-tables::table.summary.summarizers.sum.label');
    }
}
