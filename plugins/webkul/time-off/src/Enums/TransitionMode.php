<?php

namespace Webkul\TimeOff\Enums;

use Filament\Support\Contracts\HasLabel;

enum TransitionMode: string implements HasLabel
{
    case IMMEDIATELY = 'immediately';

    case END_OF_ACCRUAL = 'end_of_accrual';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::IMMEDIATELY    => __('time-off::enums/transition-mode.immediately'),
            self::END_OF_ACCRUAL => __('time-off::enums/transition-mode.end-of-accrual'),
        };
    }

    public static function options(): array
    {
        return [
            self::IMMEDIATELY->value    => __('time-off::enums/transition-mode.immediately'),
            self::END_OF_ACCRUAL->value => __('time-off::enums/transition-mode.end-of-accrual'),
        ];
    }
}
