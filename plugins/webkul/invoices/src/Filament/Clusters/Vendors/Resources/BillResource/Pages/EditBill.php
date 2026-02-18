<?php

namespace Webkul\Invoice\Filament\Clusters\Vendors\Resources\BillResource\Pages;

use Webkul\Account\Filament\Resources\BillResource\Pages\EditBill as BaseEditBill;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\BillResource;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\RefundResource;

class EditBill extends BaseEditBill
{
    protected static string $resource = BillResource::class;

    protected static string $reverseResource = RefundResource::class;
}
