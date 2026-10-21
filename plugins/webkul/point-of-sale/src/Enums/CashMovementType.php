<?php

namespace Webkul\PointOfSale\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum CashMovementType: string implements HasColor, HasIcon, HasLabel
{
    case IN = 'in';

    case OUT = 'out';

    public function getLabel(): string
    {
        return match ($this) {
            self::IN  => __('point-of-sale::enums/cash-movement-type.in'),
            self::OUT => __('point-of-sale::enums/cash-movement-type.out'),
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::IN  => 'success',
            self::OUT => 'danger',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::IN  => 'heroicon-o-arrow-down-tray',
            self::OUT => 'heroicon-o-arrow-up-tray',
        };
    }

    public function signedAmount(float $amount): float
    {
        return $this === self::IN ? $amount : -$amount;
    }
}
