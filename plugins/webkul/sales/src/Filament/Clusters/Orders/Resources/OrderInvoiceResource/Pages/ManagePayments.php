<?php

namespace Webkul\Sale\Filament\Clusters\Orders\Resources\OrderInvoiceResource\Pages;

use Webkul\Sale\Filament\Clusters\Orders\Resources\OrderInvoiceResource;
use Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationInvoiceResource\Pages\ManagePayments as BaseManagePayments;

class ManagePayments extends BaseManagePayments
{
    protected static string $resource = OrderInvoiceResource::class;
}
