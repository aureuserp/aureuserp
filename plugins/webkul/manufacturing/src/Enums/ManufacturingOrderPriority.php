<?php

namespace Webkul\Manufacturing\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum ManufacturingOrderPriority: string implements HasColor, HasLabel
{
    case NORMAL = '0';
    case URGENT = '1';

    public static function options(): array
    {
        return [
            self::NORMAL->value => __('manufacturing::enums/manufacturing-order-priority.normal'),
            self::URGENT->value => __('manufacturing::enums/manufacturing-order-priority.urgent'),
        ];
    }

    public function getLabel(): string
    {
        return self::options()[$this->value];
    }

    public function getColor(): string
    {
        return match ($this) {
            self::NORMAL => 'gray',
            self::URGENT => 'danger',
        };
    }
}
