<?php

namespace Webkul\Account\Enums;

use Filament\Support\Contracts\HasLabel;

enum InvoicePolicy: string implements HasLabel
{
    case ORDER = 'order';

    case DELIVERY = 'delivery';

    public function getLabel(): string
    {
        return match ($this) {
            self::ORDER       => __('accounts::enums/invoice-policy.order'),
            self::DELIVERY    => __('accounts::enums/invoice-policy.delivery'),
        };
    }

    public function options(): array
    {
        return [
            self::ORDER->value       => __('accounts::enums/invoice-policy.order'),
            self::DELIVERY->value    => __('accounts::enums/invoice-policy.delivery'),
        ];
    }
}
