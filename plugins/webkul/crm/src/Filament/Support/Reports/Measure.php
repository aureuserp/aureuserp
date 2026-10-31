<?php

namespace Webkul\Crm\Filament\Support\Reports;

use Webkul\Crm\Enums\MeasureFormat;

class Measure
{
    public function __construct(
        public readonly string $label,
        public readonly string $aggregate,
        public readonly MeasureFormat $format = MeasureFormat::INTEGER,
    ) {}

    public function round(mixed $value): float
    {
        return round((float) ($value ?? 0), $this->format === MeasureFormat::INTEGER ? 0 : 2);
    }
}
