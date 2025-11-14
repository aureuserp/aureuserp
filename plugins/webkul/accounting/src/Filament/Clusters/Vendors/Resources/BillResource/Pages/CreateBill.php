<?php

namespace Webkul\Accounting\Filament\Clusters\Vendors\Resources\BillResource\Pages;

use Webkul\Account\Filament\Resources\BillResource\Pages\CreateBill as BaseCreateBill;
use Webkul\Accounting\Filament\Clusters\Vendors\Resources\BillResource;

class CreateBill extends BaseCreateBill
{
    protected static string $resource = BillResource::class;
}
