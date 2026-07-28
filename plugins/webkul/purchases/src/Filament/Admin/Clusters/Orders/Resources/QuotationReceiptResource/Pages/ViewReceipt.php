<?php

namespace Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\QuotationReceiptResource\Pages;

use Webkul\Inventory\Filament\Clusters\Operations\Resources\ReceiptResource\Pages\ViewReceipt as BaseViewReceipt;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\QuotationReceiptResource;

class ViewReceipt extends BaseViewReceipt
{
    protected static string $resource = QuotationReceiptResource::class;
}
