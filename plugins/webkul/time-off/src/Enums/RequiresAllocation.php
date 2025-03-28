<?php

namespace Webkul\TimeOff\Enums;

use Filament\Support\Contracts\HasLabel;

enum RequiresAllocation: string implements HasLabel
{
    case YES = 'yes';

    case NO = 'no';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::YES => __('time-off::enums/requires-allocation.yes'),
            self::NO  => __('time-off::enums/requires-allocation.no'),
        };
    }

    public static function options(): array
    {
        return [
            self::YES->value => __('time-off::enums/requires-allocation.yes'),
            self::NO->value  => __('time-off::enums/requires-allocation.no'),
        ];
    }
}
