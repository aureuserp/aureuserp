<?php

namespace Webkul\TimeOff\Enums;

use Filament\Support\Contracts\HasLabel;

enum AccrualValidityType: string implements HasLabel
{
    case DAYS = 'days';

    case MONTHS = 'months';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::DAYS   => __('time-off::enums/accrual-validity-type.days'),
            self::MONTHS => __('time-off::enums/accrual-validity-type.months'),
        };
    }

    public static function options(): array
    {
        return [
            self::DAYS->value   => __('time-off::enums/accrual-validity-type.days'),
            self::MONTHS->value => __('time-off::enums/accrual-validity-type.months'),
        ];
    }
}
