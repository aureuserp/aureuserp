<?php

namespace Webkul\Invoice\Filament\Clusters\Vendors\Resources\VendorBillResource\Pages;

use Webkul\Invoice\Filament\Clusters\Vendors\Resources\BillResource\Pages\ViewBill as BaseViewBill;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\VendorBillResource;

class ViewBill extends BaseViewBill
{
    protected static string $resource = VendorBillResource::class;
}
