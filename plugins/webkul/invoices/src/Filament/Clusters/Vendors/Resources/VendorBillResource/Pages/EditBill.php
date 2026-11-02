<?php

namespace Webkul\Invoice\Filament\Clusters\Vendors\Resources\VendorBillResource\Pages;

use Webkul\Invoice\Filament\Clusters\Vendors\Resources\BillResource\Pages\EditBill as BaseEditBill;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\VendorBillResource;

class EditBill extends BaseEditBill
{
    protected static string $resource = VendorBillResource::class;
}
