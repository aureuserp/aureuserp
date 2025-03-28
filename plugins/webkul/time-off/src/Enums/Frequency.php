<?php

namespace Webkul\TimeOff\Enums;

use Filament\Support\Contracts\HasLabel;

enum Frequency: string implements HasLabel
{
    case HOURLY = 'hourly';

    case DAILY = 'daily';

    case WEEKLY = 'weekly';

    case BIMONTHLY = 'bimonthly';

    case MONTHLY = 'monthly';

    case BIYEARLY = 'biyearly';

    case YEARLY = 'yearly';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::HOURLY    => __('time-off::enums/frequency.hourly'),
            self::DAILY     => __('time-off::enums/frequency.daily'),
            self::WEEKLY    => __('time-off::enums/frequency.weekly'),
            self::BIMONTHLY => __('time-off::enums/frequency.bimonthly'),
            self::MONTHLY   => __('time-off::enums/frequency.monthly'),
            self::BIYEARLY  => __('time-off::enums/frequency.biyearly'),
            self::YEARLY    => __('time-off::enums/frequency.yearly'),
        };
    }

    public static function options(): array
    {
        return [
            self::HOURLY->value    => __('time-off::enums/frequency.hourly'),
            self::DAILY->value     => __('time-off::enums/frequency.daily'),
            self::WEEKLY->value    => __('time-off::enums/frequency.weekly'),
            self::BIMONTHLY->value => __('time-off::enums/frequency.bimonthly'),
            self::MONTHLY->value   => __('time-off::enums/frequency.monthly'),
            self::BIYEARLY->value  => __('time-off::enums/frequency.biyearly'),
            self::YEARLY->value    => __('time-off::enums/frequency.yearly'),
        ];
    }
}
