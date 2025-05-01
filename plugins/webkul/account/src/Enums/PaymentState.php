<?php

namespace Webkul\Account\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum PaymentState: string implements HasColor, HasIcon, HasLabel
{
    case NOT_PAID = 'not_paid';

    case IN_PAYMENT = 'in_payment';

    case PAID = 'paid';

    case PARTIAL = 'partial';

    case REVERSED = 'reversed';

    case BLOCKED = 'blocked';

    case INVOICING_LEGACY = 'invoicing_legacy';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::NOT_PAID         => __('accounts::enums/payment-state.not-paid'),
            self::IN_PAYMENT       => __('accounts::enums/payment-state.in-payment'),
            self::PAID             => __('accounts::enums/payment-state.paid'),
            self::PARTIAL          => __('accounts::enums/payment-state.partial'),
            self::REVERSED         => __('accounts::enums/payment-state.reversed'),
            self::BLOCKED          => __('accounts::enums/payment-state.blocked'),
            self::INVOICING_LEGACY => __('accounts::enums/payment-state.invoicing-legacy'),
        };
    }

    public static function options(): array
    {
        return [
            self::NOT_PAID->value         => __('accounts::enums/payment-state.not-paid'),
            self::IN_PAYMENT->value       => __('accounts::enums/payment-state.in-payment'),
            self::PAID->value             => __('accounts::enums/payment-state.paid'),
            self::PARTIAL->value          => __('accounts::enums/payment-state.partial'),
            self::REVERSED->value         => __('accounts::enums/payment-state.reversed'),
            self::BLOCKED->value          => __('accounts::enums/payment-state.blocked'),
            self::INVOICING_LEGACY->value => __('accounts::enums/payment-state.invoicing-legacy'),
        ];
    }

    public function getColor(): ?string
    {
        return match ($this) {
            self::NOT_PAID         => 'gray',
            self::IN_PAYMENT       => 'warning',
            self::PAID             => 'success',
            self::PARTIAL          => 'info',
            self::REVERSED         => 'danger',
            self::BLOCKED          => 'danger',
            self::INVOICING_LEGACY => 'gray',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::NOT_PAID         => 'heroicon-o-x-circle',
            self::IN_PAYMENT       => 'heroicon-o-clock',
            self::PAID             => 'heroicon-o-check-badge',
            self::PARTIAL          => 'heroicon-o-currency-dollar',
            self::REVERSED         => 'heroicon-o-arrow-uturn-left',
            self::BLOCKED          => 'heroicon-o-x-circle',
            self::INVOICING_LEGACY => 'heroicon-o-document-text',
        };
    }
}
