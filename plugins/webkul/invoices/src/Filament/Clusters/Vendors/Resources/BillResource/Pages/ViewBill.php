<?php

namespace Webkul\Invoice\Filament\Clusters\Vendors\Resources\BillResource\Pages;

use Webkul\Account\Filament\Resources\BillResource\Pages\ViewBill as BaseViewBill;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\BillResource;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\RefundResource;

class ViewBill extends BaseViewBill
{
    protected static string $resource = BillResource::class;

    protected static string $reverseResource = RefundResource::class;
}
