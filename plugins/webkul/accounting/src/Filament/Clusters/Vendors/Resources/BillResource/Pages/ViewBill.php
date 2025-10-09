<?php

namespace Webkul\Accounting\Filament\Clusters\Vendors\Resources\BillResource\Pages;

use Webkul\Account\Filament\Resources\BillResource\Pages\ViewBill as BaseViewBill;
use Webkul\Accounting\Filament\Clusters\Vendors\Resources\BillResource;

class ViewBill extends BaseViewBill
{
    protected static string $resource = BillResource::class;
}
