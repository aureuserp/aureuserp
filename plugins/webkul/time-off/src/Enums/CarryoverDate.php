<?php

namespace Webkul\TimeOff\Enums;

use Filament\Support\Contracts\HasLabel;

enum CarryoverDate: string implements HasLabel
{
    case YEAR_START = 'year_start';

    case ALLOCATION = 'allocation';

    case OTHER = 'other';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::YEAR_START => __('time-off::enums/carry-over-date.year-start'),
            self::ALLOCATION => __('time-off::enums/carry-over-date.allocation'),
            self::OTHER      => __('time-off::enums/carry-over-date.other'),
        };
    }

    public static function options(): array
    {
        return [
            self::YEAR_START->value => __('time-off::enums/carry-over-date.year-start'),
            self::ALLOCATION->value => __('time-off::enums/carry-over-date.allocation'),
            self::OTHER->value      => __('time-off::enums/carry-over-date.other'),
        ];
    }
}
