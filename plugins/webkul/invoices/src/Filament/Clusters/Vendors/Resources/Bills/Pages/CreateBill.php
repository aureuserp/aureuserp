<?php

namespace Webkul\Invoice\Filament\Clusters\Vendors\Resources\Bills\Pages;

use Webkul\Account\Filament\Resources\Bills\Pages\CreateBill as BaseCreateBill;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\Bills\BillResource;

class CreateBill extends BaseCreateBill
{
    protected static string $resource = BillResource::class;
}
