<?php

namespace Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\PurchaseOrderReceiptResource\Pages;

use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\PurchaseOrderReceiptResource;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\QuotationReceiptResource\Pages\EditReceipt as BaseEditReceipt;

class EditReceipt extends BaseEditReceipt
{
    protected static string $resource = PurchaseOrderReceiptResource::class;
}
