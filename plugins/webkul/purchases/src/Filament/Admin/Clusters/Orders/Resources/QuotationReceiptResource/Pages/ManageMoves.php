<?php

namespace Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\QuotationReceiptResource\Pages;

use Webkul\Inventory\Filament\Clusters\Operations\Resources\ReceiptResource\Pages\ManageMoves as BaseManageMoves;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\QuotationReceiptResource;

class ManageMoves extends BaseManageMoves
{
    protected static string $resource = QuotationReceiptResource::class;
}
