<?php

namespace Webkul\TimeOff\Enums;

use Filament\Support\Contracts\HasLabel;

enum AllocationType: string implements HasLabel
{
    case REGULAR = 'regular';

    case ACCRUAL = 'accrual';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::REGULAR => __('time-off::enums/allocation-type.regular'),
            self::ACCRUAL => __('time-off::enums/allocation-type.accrual'),
        };
    }

    public static function options(): array
    {
        return [
            self::REGULAR->value => __('time-off::enums/allocation-type.regular'),
            self::ACCRUAL->value => __('time-off::enums/allocation-type.accrual'),
        ];
    }
}
