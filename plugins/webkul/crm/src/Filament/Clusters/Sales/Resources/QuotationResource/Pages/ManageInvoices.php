<?php

namespace Webkul\Crm\Filament\Clusters\Sales\Resources\QuotationResource\Pages;

use Webkul\Crm\Filament\Clusters\Sales\Resources\QuotationInvoiceResource;
use Webkul\Crm\Filament\Clusters\Sales\Resources\QuotationResource;
use Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationResource\Pages\ManageInvoices as BaseManageInvoices;

class ManageInvoices extends BaseManageInvoices
{
    protected static string $resource = QuotationResource::class;

    protected static ?string $relatedResource = QuotationInvoiceResource::class;
}
