<?php

namespace Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\QuotationBillResource\Pages;

use Webkul\Invoice\Filament\Clusters\Vendors\Resources\BillResource\Pages\ViewBill as BaseViewBill;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\QuotationBillResource;

class ViewBill extends BaseViewBill
{
    protected static string $resource = QuotationBillResource::class;
}
