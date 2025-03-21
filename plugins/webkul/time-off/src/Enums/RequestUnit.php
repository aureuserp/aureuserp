<?php

namespace Webkul\TimeOff\Enums;

use Filament\Support\Contracts\HasLabel;

enum RequestUnit: string implements HasLabel
{
    case DAY = 'day';

    case HALF_DAY = 'half_day';

    case HOUR = 'hour';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::DAY      => __('time-off::enums/request-unit.day'),
            self::HALF_DAY => __('time-off::enums/request-unit.half-day'),
            self::HOUR     => __('time-off::enums/request-unit.hour'),
        };
    }

    public static function options(): array
    {
        return [
            self::DAY->value      => __('time-off::enums/request-unit.day'),
            self::HALF_DAY->value => __('time-off::enums/request-unit.half-day'),
            self::HOUR->value     => __('time-off::enums/request-unit.hour'),
        ];
    }
}
