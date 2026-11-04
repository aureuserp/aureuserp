<?php

namespace Webkul\Crm\Enums;

use Webkul\Sale\Enums\InvoiceStatus;
use Webkul\Sale\Enums\OrderState;

enum TeamOrderScope: string
{
    case QUOTATIONS = 'quotations';

    case TO_INVOICE = 'to-invoice';

    case SALES_ORDERS = 'sales-orders';

    public function getFilterRules(): array
    {
        [$constraint, $values] = match ($this) {
            self::QUOTATIONS   => ['state', [OrderState::DRAFT->value, OrderState::SENT->value]],
            self::TO_INVOICE   => ['invoice_status', [InvoiceStatus::TO_INVOICE->value]],
            self::SALES_ORDERS => ['state', [OrderState::SALE->value]],
        };

        return [$constraint => ['type' => $constraint, 'data' => ['operator' => 'is', 'settings' => ['values' => $values]]]];
    }
}
