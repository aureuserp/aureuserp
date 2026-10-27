<?php

namespace Webkul\Crm\Filament\Support\Reports;

use Illuminate\Support\Number;
use Webkul\Crm\Enums\MeasureFormat;

class Measure
{
    public function __construct(
        public readonly string $label,
        public readonly string $aggregate,
        public readonly MeasureFormat $format = MeasureFormat::INTEGER,
    ) {}

    public function format(mixed $value, ?string $currency = null): string
    {
        $value = (float) ($value ?? 0);

        return match ($this->format) {
            MeasureFormat::CURRENCY => Number::currency($value, $currency ?? 'USD'),
            MeasureFormat::DAYS     => Number::format($value, maxPrecision: 1),
            MeasureFormat::PERCENT  => Number::format($value, maxPrecision: 1).'%',
            MeasureFormat::INTEGER  => Number::format($value, maxPrecision: 0),
        };
    }

    public function round(mixed $value): float
    {
        return round((float) ($value ?? 0), $this->format === MeasureFormat::INTEGER ? 0 : 2);
    }
}
