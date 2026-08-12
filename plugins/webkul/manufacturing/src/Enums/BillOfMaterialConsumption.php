<?php

namespace Webkul\Manufacturing\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum BillOfMaterialConsumption: string implements HasColor, HasLabel
{
    case FLEXIBLE = 'flexible';
    case WARNING = 'warning';
    case STRICT = 'strict';

    public static function options(): array
    {
        return [
            self::FLEXIBLE->value => __('manufacturing::enums/bill-of-material-consumption.flexible'),
            self::WARNING->value  => __('manufacturing::enums/bill-of-material-consumption.warning'),
            self::STRICT->value   => __('manufacturing::enums/bill-of-material-consumption.strict'),
        ];
    }

    public function getLabel(): string
    {
        return self::options()[$this->value];
    }

    public function getColor(): string
    {
        return match ($this) {
            self::FLEXIBLE => 'success',
            self::WARNING  => 'warning',
            self::STRICT   => 'danger',
        };
    }
}
