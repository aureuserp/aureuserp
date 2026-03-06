<?php

namespace Webkul\Purchase\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum OrderState: string implements HasColor, HasLabel
{
    case DRAFT = 'draft';

    case SENT = 'sent';

    case TO_APPROVE = 'to_approve';

    case PURCHASE = 'purchase';

    case DONE = 'done';

    case CANCELED = 'canceled';

    public static function options(): array
    {
        return [
            self::DRAFT->value      => __('purchases::enums/order-state.draft'),
            self::SENT->value       => __('purchases::enums/order-state.sent'),
            self::TO_APPROVE->value => __('purchases::enums/order-state.to_approve'),
            self::PURCHASE->value   => __('purchases::enums/order-state.purchase'),
            self::DONE->value       => __('purchases::enums/order-state.done'),
            self::CANCELED->value   => __('purchases::enums/order-state.canceled'),
        ];
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::DRAFT      => __('purchases::enums/order-state.draft'),
            self::SENT       => __('purchases::enums/order-state.sent'),
            self::TO_APPROVE => __('purchases::enums/order-state.to_approve'),
            self::PURCHASE   => __('purchases::enums/order-state.purchase'),
            self::DONE       => __('purchases::enums/order-state.done'),
            self::CANCELED   => __('purchases::enums/order-state.canceled'),
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::DRAFT      => 'gray',
            self::SENT       => 'warning',
            self::TO_APPROVE => 'info',
            self::PURCHASE   => 'success',
            self::DONE       => 'success',
            self::CANCELED   => 'danger',
        };
    }
}
