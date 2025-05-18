<?php

namespace Webkul\Account\Enums;

use Filament\Support\Contracts\HasLabel;

enum PaymentStatus: string implements HasLabel
{
    case DRAFT = 'draft';

    case IN_PROCESS = 'in_process';

    case PAID = 'paid';

    case NOT_PAID = 'not_paid';

    case CANCELED = 'canceled';

    case REJECTED = 'rejected';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::DRAFT      => __('accounts::enums/payment-status.draft'),
            self::IN_PROCESS => __('accounts::enums/payment-status.in-process'),
            self::PAID       => __('accounts::enums/payment-status.paid'),
            self::NOT_PAID   => __('accounts::enums/payment-status.not-paid'),
            self::CANCELED   => __('accounts::enums/payment-status.canceled'),
            self::REJECTED   => __('accounts::enums/payment-status.rejected'),
        };
    }

    public static function options(): array
    {
        return [
            self::DRAFT->value      => __('accounts::enums/payment-status.draft'),
            self::IN_PROCESS->value => __('accounts::enums/payment-status.in-process'),
            self::PAID->value       => __('accounts::enums/payment-status.paid'),
            self::NOT_PAID->value   => __('accounts::enums/payment-status.not-paid'),
            self::CANCELED->value   => __('accounts::enums/payment-status.canceled'),
            self::REJECTED->value   => __('accounts::enums/payment-status.rejected'),
        ];
    }
}
