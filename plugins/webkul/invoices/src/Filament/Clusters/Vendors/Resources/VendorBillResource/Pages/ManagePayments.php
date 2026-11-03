<?php

namespace Webkul\Invoice\Filament\Clusters\Vendors\Resources\VendorBillResource\Pages;

use Webkul\Invoice\Filament\Clusters\Vendors\Resources\BillResource\Pages\ManagePayments as BaseManagePayments;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\VendorBillResource;

class ManagePayments extends BaseManagePayments
{
    protected static string $resource = VendorBillResource::class;
}
