<?php

namespace Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\QuotationReceiptResource\Pages;

use Webkul\Inventory\Filament\Clusters\Operations\Resources\ReceiptResource\Pages\EditReceipt as BaseEditReceipt;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\QuotationReceiptResource;

class EditReceipt extends BaseEditReceipt
{
    protected static string $resource = QuotationReceiptResource::class;
}
