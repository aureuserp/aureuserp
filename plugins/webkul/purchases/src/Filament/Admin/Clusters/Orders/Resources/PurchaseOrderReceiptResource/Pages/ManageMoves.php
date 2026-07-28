<?php

namespace Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\PurchaseOrderReceiptResource\Pages;

use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\PurchaseOrderReceiptResource;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\QuotationReceiptResource\Pages\ManageMoves as BaseManageMoves;

class ManageMoves extends BaseManageMoves
{
    protected static string $resource = PurchaseOrderReceiptResource::class;
}
