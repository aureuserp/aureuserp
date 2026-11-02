<?php

namespace Webkul\Accounting\Filament\Clusters\Vendors\Resources\VendorBillResource\Pages;

use Webkul\Accounting\Filament\Clusters\Vendors\Resources\BillResource\Pages\ManagePayments as BaseManagePayments;
use Webkul\Accounting\Filament\Clusters\Vendors\Resources\VendorBillResource;

class ManagePayments extends BaseManagePayments
{
    protected static string $resource = VendorBillResource::class;
}
