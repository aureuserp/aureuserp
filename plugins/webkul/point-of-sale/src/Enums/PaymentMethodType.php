<?php

namespace Webkul\PointOfSale\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum PaymentMethodType: string implements HasColor, HasIcon, HasLabel
{
    case CASH = 'cash';

    case BANK = 'bank';

    case PAY_LATER = 'pay_later';

    public function getLabel(): string
    {
        return match ($this) {
            self::CASH      => __('point-of-sale::enums/payment-method-type.cash'),
            self::BANK      => __('point-of-sale::enums/payment-method-type.bank'),
            self::PAY_LATER => __('point-of-sale::enums/payment-method-type.pay-later'),
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::CASH      => 'success',
            self::BANK      => 'primary',
            self::PAY_LATER => 'warning',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::CASH      => 'heroicon-o-banknotes',
            self::BANK      => 'heroicon-o-credit-card',
            self::PAY_LATER => 'heroicon-o-clock',
        };
    }
}
