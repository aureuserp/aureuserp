<?php

namespace Webkul\Crm\Filament\Support\Reports;

use Illuminate\Support\Number;

class Measure
{
    public const FORMAT_INTEGER = 'integer';

    public const FORMAT_CURRENCY = 'currency';

    public const FORMAT_DAYS = 'days';

    public const FORMAT_PERCENT = 'percent';

    public function __construct(
        public readonly string $label,
        public readonly string $aggregate,
        public readonly string $format = self::FORMAT_INTEGER,
    ) {}

    public function format(mixed $value, ?string $currency = null): string
    {
        $value = (float) ($value ?? 0);

        return match ($this->format) {
            self::FORMAT_CURRENCY => Number::currency($value, $currency ?? 'USD'),
            self::FORMAT_DAYS     => Number::format($value, maxPrecision: 1),
            self::FORMAT_PERCENT  => Number::format($value, maxPrecision: 1).'%',
            default               => Number::format($value, maxPrecision: 0),
        };
    }

    public function round(mixed $value): float
    {
        return round((float) ($value ?? 0), $this->format === self::FORMAT_INTEGER ? 0 : 2);
    }
}
