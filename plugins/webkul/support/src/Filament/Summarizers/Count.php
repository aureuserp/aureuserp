<?php

namespace Webkul\Support\Filament\Summarizers;

use Illuminate\Support\Collection;

class Count extends Summarizer
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->numeric();
    }

    protected function calculateSummary(Collection $items, string $columnName): ?int
    {
        return $items->count();
    }

    public function getDefaultLabel(): ?string
    {
        return __('filament-tables::table.summary.summarizers.count.label');
    }
}
