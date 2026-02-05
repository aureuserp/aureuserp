<?php

namespace Webkul\Account\Filament\Resources\RefundResource\Pages;

use Webkul\Account\Filament\Resources\BillResource\Pages\ListBills as ListRecords;
use Webkul\Account\Filament\Resources\RefundResource;

class ListRefunds extends ListRecords
{
    protected static string $resource = RefundResource::class;
}
