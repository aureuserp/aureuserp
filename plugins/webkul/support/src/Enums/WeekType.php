<?php

namespace Webkul\Support\Enums;

enum WeekType: string
{
    case All = 'all';

    case Even = 'even';

    case Odd = 'odd';

    public static function options(): array
    {
        return [
            self::All->value  => __('support::enums/week-type.all'),
            self::Even->value => __('support::enums/week-type.even'),
            self::Odd->value  => __('support::enums/week-type.odd'),
        ];
    }
}
