<?php

namespace Webkul\Sale\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum OrderState: string implements HasColor, HasLabel
{
    case DRAFT = 'draft';

    case SENT = 'sent';

    case SALE = 'sale';

    case CANCEL = 'cancel';

    public function getLabel(): string
    {
        return match ($this) {
            self::DRAFT   => __('sales::enums/order-state.draft'),
            self::SENT    => __('sales::enums/order-state.sent'),
            self::SALE    => __('sales::enums/order-state.sale'),
            self::CANCEL  => __('sales::enums/order-state.cancel'),
        };
    }

    public static function options(): array
    {
        return [
            self::DRAFT->value   => __('sales::enums/order-state.draft'),
            self::SENT->value    => __('sales::enums/order-state.sent'),
            self::SALE->value    => __('sales::enums/order-state.sale'),
            self::CANCEL->value  => __('sales::enums/order-state.cancel'),
        ];
    }

    public function getColor(): ?string
    {
        return match ($this) {
            self::DRAFT  => 'gray',
            self::SENT   => 'warning',
            self::SALE   => 'success',
            self::CANCEL => 'danger',
        };
    }
}
