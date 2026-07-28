<?php

namespace Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\PurchaseOrderReceiptResource\Pages;

use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\PurchaseOrderReceiptResource;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\QuotationReceiptResource\Pages\ViewReceipt as BaseViewReceipt;

class ViewReceipt extends BaseViewReceipt
{
    protected static string $resource = PurchaseOrderReceiptResource::class;
}
