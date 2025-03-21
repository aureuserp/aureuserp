<?php

namespace Webkul\TimeOff\Enums;

use Filament\Support\Contracts\HasLabel;

enum RequestDateFromPeriod: string implements HasLabel
{
    case MORNING = 'morning';

    case AFTERNOON = 'afternoon';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::MORNING   => __('time-off::enums/request-date-from-period.morning'),
            self::AFTERNOON => __('time-off::enums/request-date-from-period.afternoon'),
        };
    }

    public static function options(): array
    {
        return [
            self::MORNING->value   => __('time-off::enums/request-date-from-period.morning'),
            self::AFTERNOON->value => __('time-off::enums/request-date-from-period.afternoon'),
        ];
    }
}
