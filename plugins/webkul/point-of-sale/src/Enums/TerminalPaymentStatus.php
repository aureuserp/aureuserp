<?php

namespace Webkul\PointOfSale\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum TerminalPaymentStatus: string implements HasColor, HasLabel
{
    case PENDING = 'pending';

    case AUTHORIZED = 'authorized';

    case DONE = 'done';

    case REVERSED = 'reversed';

    case FAILED = 'failed';

    public function getLabel(): string
    {
        return match ($this) {
            self::PENDING    => __('point-of-sale::enums/terminal-payment-status.pending'),
            self::AUTHORIZED => __('point-of-sale::enums/terminal-payment-status.authorized'),
            self::DONE       => __('point-of-sale::enums/terminal-payment-status.done'),
            self::REVERSED   => __('point-of-sale::enums/terminal-payment-status.reversed'),
            self::FAILED     => __('point-of-sale::enums/terminal-payment-status.failed'),
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::PENDING    => 'warning',
            self::AUTHORIZED => 'info',
            self::DONE       => 'success',
            self::REVERSED   => 'gray',
            self::FAILED     => 'danger',
        };
    }
}
