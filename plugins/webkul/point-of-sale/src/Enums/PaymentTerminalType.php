<?php

namespace Webkul\PointOfSale\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum PaymentTerminalType: string implements HasColor, HasLabel
{
    case NONE = 'none';

    case TERMINAL = 'terminal';

    case QR_CODE = 'qr_code';

    public function getLabel(): string
    {
        return match ($this) {
            self::NONE     => __('point-of-sale::enums/payment-terminal-type.none'),
            self::TERMINAL => __('point-of-sale::enums/payment-terminal-type.terminal'),
            self::QR_CODE  => __('point-of-sale::enums/payment-terminal-type.qr-code'),
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::NONE     => 'gray',
            self::TERMINAL => 'primary',
            self::QR_CODE  => 'info',
        };
    }
}
