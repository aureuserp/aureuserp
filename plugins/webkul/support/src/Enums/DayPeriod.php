<?php

namespace Webkul\Support\Enums;

enum DayPeriod: string
{
    case Morning = 'morning';

    case Afternoon = 'afternoon';

    case Evening = 'evening';

    case Night = 'night';

    public static function options(): array
    {
        return [
            self::Morning->value   => __('support::enums/day-period.morning'),
            self::Afternoon->value => __('support::enums/day-period.afternoon'),
            self::Evening->value   => __('support::enums/day-period.evening'),
            self::Night->value     => __('support::enums/day-period.night'),
        ];
    }
}
