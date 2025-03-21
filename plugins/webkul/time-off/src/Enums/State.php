<?php

namespace Webkul\TimeOff\Enums;

use Filament\Support\Contracts\HasLabel;

enum State: string implements HasLabel
{
    case CONFIRM = 'confirm';

    case REFUSE = 'refuse';

    case VALIDATE_ONE = 'validate_one';

    case VALIDATE_TWO = 'validate_two';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::CONFIRM      => __('time-off::enums/state.confirm'),
            self::REFUSE       => __('time-off::enums/state.refuse'),
            self::VALIDATE_ONE => __('time-off::enums/state.validate_one'),
            self::VALIDATE_TWO => __('time-off::enums/state.validate_two'),
        };
    }

    public static function options(): array
    {
        return [
            self::CONFIRM->value      => __('time-off::enums/state.confirm'),
            self::REFUSE->value       => __('time-off::enums/state.refuse'),
            self::VALIDATE_ONE->value => __('time-off::enums/state.validate_one'),
            self::VALIDATE_TWO->value => __('time-off::enums/state.validate_two'),
        ];
    }
}
