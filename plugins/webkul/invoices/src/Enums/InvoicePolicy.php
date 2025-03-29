<?php

namespace Webkul\Invoice\Enums;

use Filament\Support\Contracts\HasLabel;

enum InvoicePolicy: string implements HasLabel
{
    case ORDER = 'order';

    case DELIVERY = 'delivery';

    public function getLabel(): string
    {
        return match ($this) {
            self::ORDER       => __('invoices::enums/invoice-policy.order'),
            self::DELIVERY    => __('invoices::enums/invoice-policy.delivery'),
        };
    }

    public function options(): array
    {
        return [
            self::ORDER->value       => __('invoices::enums/invoice-policy.order'),
            self::DELIVERY->value    => __('invoices::enums/invoice-policy.delivery'),
        ];
    }
}
