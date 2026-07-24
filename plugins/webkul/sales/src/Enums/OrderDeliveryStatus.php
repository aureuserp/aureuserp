<?php

namespace Webkul\Sale\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum OrderDeliveryStatus: string implements HasColor, HasLabel
{
    case NO = 'no';

    case PENDING = 'pending';

    case STARTED = 'started';

    case PARTIAL = 'partial';

    case FULL = 'full';

    public static function options(): array
    {
        return [
            self::NO->value      => __('sales::enums/order-delivery-status.no'),
            self::PENDING->value => __('sales::enums/order-delivery-status.pending'),
            self::STARTED->value => __('sales::enums/order-delivery-status.started'),
            self::PARTIAL->value => __('sales::enums/order-delivery-status.partial'),
            self::FULL->value    => __('sales::enums/order-delivery-status.full'),
        ];
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::NO      => __('sales::enums/order-delivery-status.no'),
            self::PENDING => __('sales::enums/order-delivery-status.pending'),
            self::STARTED => __('sales::enums/order-delivery-status.started'),
            self::PARTIAL => __('sales::enums/order-delivery-status.partial'),
            self::FULL    => __('sales::enums/order-delivery-status.full'),
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::NO      => 'gray',
            self::PENDING => 'info',
            self::STARTED => 'info',
            self::PARTIAL => 'warning',
            self::FULL    => 'success',
        };
    }
}
