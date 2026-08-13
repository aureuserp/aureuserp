<?php

namespace Webkul\Support\Enums;

enum DayOfWeek: string
{
    case Monday = 'monday';

    case Tuesday = 'tuesday';

    case Wednesday = 'wednesday';

    case Thursday = 'thursday';

    case Friday = 'friday';

    case Saturday = 'saturday';

    case Sunday = 'sunday';

    public static function options(): array
    {
        return [
            self::Monday->value     => __('support::enums/day-of-week.monday'),
            self::Tuesday->value    => __('support::enums/day-of-week.tuesday'),
            self::Wednesday->value  => __('support::enums/day-of-week.wednesday'),
            self::Thursday->value   => __('support::enums/day-of-week.thursday'),
            self::Friday->value     => __('support::enums/day-of-week.friday'),
            self::Saturday->value   => __('support::enums/day-of-week.saturday'),
            self::Sunday->value     => __('support::enums/day-of-week.sunday'),
        ];
    }
}
