<?php

namespace Webkul\Sale\Enums;

use Filament\Support\Contracts\HasLabel;

enum QtyDeliveredMethod: string implements HasLabel
{
    case MANUAL = 'manual';

    case STOCK_MOVE = 'stock_move';

    public function getLabel(): string
    {
        return match ($this) {
            self::MANUAL     => __('sales::enums/qty-delivered-method.manual'),
            self::STOCK_MOVE => __('sales::enums/qty-delivered-method.stock-move'),
        };
    }
}
