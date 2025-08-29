<?php

namespace Webkul\Purchase\Filament\Customer\Clusters\Account\Resources\Orders;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\Purchase\Filament\Customer\Clusters\Account\Resources\Orders\Schemas\OrderInfolist;
use Webkul\Purchase\Filament\Customer\Clusters\Account\Resources\Orders\Tables\OrdersTable;
use Webkul\Purchase\Models\Order;
use Webkul\Website\Filament\Customer\Clusters\Account;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $cluster = Account::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function table(Table $table): Table
    {
        return OrdersTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return OrderInfolist::configure($schema);
    }
}
