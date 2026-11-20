<?php

namespace Webkul\Accounting\Filament\Clusters\Vendors\Resources\VendorBillResource\Pages;

use Webkul\Accounting\Filament\Clusters\Vendors\Resources\BillResource\Pages\ViewBill as BaseViewBill;
use Webkul\Accounting\Filament\Clusters\Vendors\Resources\VendorBillResource;

class ViewBill extends BaseViewBill
{
    protected static string $resource = VendorBillResource::class;
}
