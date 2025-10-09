<?php

namespace Webkul\Accounting\Filament\Clusters\Vendors\Resources\BillResource\Pages;

use Webkul\Account\Filament\Resources\BillResource\Pages\EditBill as BaseEditBill;
use Webkul\Accounting\Filament\Clusters\Vendors\Resources\BillResource;

class EditBill extends BaseEditBill
{
    protected static string $resource = BillResource::class;
}
