<?php

namespace Webkul\Account\Filament\Resources\Bills\Tables;

use Filament\Tables\Table;
use Webkul\Invoice\Filament\Clusters\Customer\Resources\Invoices\InvoiceResource;

class BillsTable
{
    public static function configure(Table $table): Table
    {
        return InvoiceResource::table($table);
    }
}
