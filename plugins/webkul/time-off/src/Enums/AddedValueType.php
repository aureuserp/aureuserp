<?php

namespace Webkul\TimeOff\Enums;

use Filament\Support\Contracts\HasLabel;

enum AddedValueType: string implements HasLabel
{
    case DAYS = 'days';

    case HOURS = 'hours';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::DAYS  => __('time-off::enums/added-value-type.days'),
            self::HOURS => __('time-off::enums/added-value-type.hours'),
        };
    }

    public static function options(): array
    {
        return [
            self::DAYS->value  => __('time-off::enums/added-value-type.days'),
            self::HOURS->value => __('time-off::enums/added-value-type.hours'),
        ];
    }
}
