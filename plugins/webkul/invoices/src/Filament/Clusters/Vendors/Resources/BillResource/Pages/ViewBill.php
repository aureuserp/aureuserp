<?php

namespace Webkul\Invoice\Filament\Clusters\Vendors\Resources\BillResource\Pages;

use Webkul\Account\Filament\Resources\BillResource\Pages\ViewBill as BaseViewBill;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\BillResource;

class ViewBill extends BaseViewBill
{
    protected static string $resource = BillResource::class;
}
