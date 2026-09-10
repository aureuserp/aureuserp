<?php

namespace Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\PurchaseOrderBillResource\Pages;

use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\PurchaseOrderBillResource;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\QuotationBillResource\Pages\ViewBill as BaseViewBill;

class ViewBill extends BaseViewBill
{
    protected static string $resource = PurchaseOrderBillResource::class;
}
