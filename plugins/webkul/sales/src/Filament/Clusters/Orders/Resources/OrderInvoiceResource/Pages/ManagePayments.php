<?php

namespace Webkul\Sale\Filament\Clusters\Orders\Resources\OrderInvoiceResource\Pages;

use Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationInvoiceResource\Pages\ManagePayments as BaseManagePayments;
use Webkul\Sale\Filament\Clusters\Orders\Resources\OrderInvoiceResource;

class ManagePayments extends BaseManagePayments
{
    protected static string $resource = OrderInvoiceResource::class;
}
