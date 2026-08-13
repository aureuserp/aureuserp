<?php

namespace Webkul\Support\Enums;

enum CalendarDisplayType: string
{
    case Working = 'working';

    case Off = 'off';

    case Holiday = 'holiday';

    public static function options(): array
    {
        return [
            self::Working->value => __('support::enums/calendar-display-type.working'),
            self::Off->value     => __('support::enums/calendar-display-type.off'),
            self::Holiday->value => __('support::enums/calendar-display-type.holiday'),
        ];
    }
}
