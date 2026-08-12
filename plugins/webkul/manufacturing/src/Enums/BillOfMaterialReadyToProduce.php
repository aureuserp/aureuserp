<?php

namespace Webkul\Manufacturing\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum BillOfMaterialReadyToProduce: string implements HasColor, HasLabel
{
    case ALL_AVAILABLE = 'all_available';
    case ASAP = 'asap';

    public static function options(): array
    {
        return [
            self::ALL_AVAILABLE->value => __('manufacturing::enums/bill-of-material-ready-to-produce.all-available'),
            self::ASAP->value          => __('manufacturing::enums/bill-of-material-ready-to-produce.asap'),
        ];
    }

    public function getLabel(): string
    {
        return self::options()[$this->value];
    }

    public function getColor(): string
    {
        return match ($this) {
            self::ALL_AVAILABLE => 'primary',
            self::ASAP          => 'warning',
        };
    }
}
