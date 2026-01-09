<?php

namespace Webkul\Accounting\Filament\Clusters\Vendors\Resources\BillResource\Pages;

use Webkul\Account\Filament\Resources\BillResource\Pages\ViewBill as BaseViewBill;
use Webkul\Accounting\Filament\Clusters\Vendors\Resources\BillResource;
use Webkul\Accounting\Filament\Clusters\Vendors\Resources\RefundResource;

class ViewBill extends BaseViewBill
{
    protected static string $resource = BillResource::class;

    protected static string $reverseResource = RefundResource::class;
}
