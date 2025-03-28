<?php

namespace Webkul\TimeOff\Enums;

use Filament\Support\Contracts\HasLabel;

enum CarryOverUnusedAccruals: string implements HasLabel
{
    case ACCRUED_TIME_RESET_TO_ZERO = 'lost';

    case ALL_ACCRUED_TIME_CARRIED_OVER = 'all';

    case CARRY_OVER_WITH_THE_MAXIMUM = 'maximum';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::ACCRUED_TIME_RESET_TO_ZERO    => __('time-off::enums/carry-over-unused-accruals.lost'),
            self::ALL_ACCRUED_TIME_CARRIED_OVER => __('time-off::enums/carry-over-unused-accruals.all'),
            self::CARRY_OVER_WITH_THE_MAXIMUM   => __('time-off::enums/carry-over-unused-accruals.maximum'),
        };
    }

    public static function options(): array
    {
        return [
            self::ACCRUED_TIME_RESET_TO_ZERO->value    => __('time-off::enums/carry-over-unused-accruals.lost'),
            self::ALL_ACCRUED_TIME_CARRIED_OVER->value => __('time-off::enums/carry-over-unused-accruals.all'),
            self::CARRY_OVER_WITH_THE_MAXIMUM->value   => __('time-off::enums/carry-over-unused-accruals.maximum'),
        ];
    }
}
