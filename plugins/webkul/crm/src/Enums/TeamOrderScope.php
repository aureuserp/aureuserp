<?php

namespace Webkul\Crm\Enums;

use Filament\Support\Contracts\HasLabel;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Sale\Enums\InvoiceStatus;
use Webkul\Sale\Enums\OrderState;

enum TeamOrderScope: string implements HasLabel
{
    case QUOTATIONS = 'quotations';

    case TO_INVOICE = 'to-invoice';

    case SALES_ORDERS = 'sales-orders';

    public function getLabel(): string
    {
        return match ($this) {
            self::QUOTATIONS   => __('crm::enums/team-order-scope.quotations'),
            self::TO_INVOICE   => __('crm::enums/team-order-scope.to-invoice'),
            self::SALES_ORDERS => __('crm::enums/team-order-scope.sales-orders'),
        };
    }

    public function apply(Builder $query): Builder
    {
        return match ($this) {
            self::QUOTATIONS   => $query->whereIn('state', [OrderState::DRAFT, OrderState::SENT]),
            self::TO_INVOICE   => $query->where('invoice_status', InvoiceStatus::TO_INVOICE),
            self::SALES_ORDERS => $query->where('state', OrderState::SALE),
        };
    }
}
