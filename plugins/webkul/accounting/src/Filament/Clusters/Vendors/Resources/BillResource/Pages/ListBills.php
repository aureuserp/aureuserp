<?php

namespace Webkul\Accounting\Filament\Clusters\Vendors\Resources\BillResource\Pages;

use Webkul\Account\Filament\Resources\BillResource\Pages\ListBills as BaseListBills;
use Webkul\Accounting\Filament\Clusters\Vendors\Resources\BillResource;

class ListBills extends BaseListBills
{
    protected static string $resource = BillResource::class;
}
