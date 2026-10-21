<?php

namespace Webkul\PointOfSale\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum StockUpdateMode: string implements HasColor, HasLabel
{
    case REAL_TIME = 'real_time';

    case AT_CLOSING = 'at_closing';

    public function getLabel(): string
    {
        return match ($this) {
            self::REAL_TIME  => __('point-of-sale::enums/stock-update-mode.real-time'),
            self::AT_CLOSING => __('point-of-sale::enums/stock-update-mode.at-closing'),
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::REAL_TIME  => 'success',
            self::AT_CLOSING => 'warning',
        };
    }
}
