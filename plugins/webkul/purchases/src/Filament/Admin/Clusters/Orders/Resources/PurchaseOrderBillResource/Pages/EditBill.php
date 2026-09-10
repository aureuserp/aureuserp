<?php

namespace Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\PurchaseOrderBillResource\Pages;

use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\PurchaseOrderBillResource;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\QuotationBillResource\Pages\EditBill as BaseEditBill;

class EditBill extends BaseEditBill
{
    protected static string $resource = PurchaseOrderBillResource::class;
}
