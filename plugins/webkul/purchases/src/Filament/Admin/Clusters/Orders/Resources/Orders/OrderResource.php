<?php

namespace Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\Orders;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\Field\Filament\Traits\HasCustomFields;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\Orders\Schemas\OrderForm;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\Orders\Schemas\OrderInfolist;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\Orders\Tables\OrdersTable;
use Webkul\Purchase\Models\Order;

class OrderResource extends Resource
{
    use HasCustomFields;

    protected static ?string $model = Order::class;

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return OrderForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OrdersTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return OrderInfolist::configure($schema);
    }
}
