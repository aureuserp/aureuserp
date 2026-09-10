<?php

namespace Webkul\Sale\Filament\Clusters\Orders\Resources\CustomerResource\Tables;

use Filament\Tables\Table;
use Webkul\Invoice\Filament\Clusters\Customers\Resources\CustomerResource as BaseCustomerResource;

class CustomersTable
{
    public static function configure(Table $table): Table
    {
        return BaseCustomerResource::table($table)
            ->contentGrid([
                'sm'  => 1,
                'md'  => 2,
                'xl'  => 3,
                '2xl' => 3,
            ]);
    }
}
