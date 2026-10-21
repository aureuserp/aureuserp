<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources;

use BackedEnum;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\PointOfSale\Filament\Admin\Clusters\Orders;
use Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources\OrderResource\Pages\EditOrder;
use Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources\OrderResource\Pages\ListOrders;
use Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources\OrderResource\Pages\ManageDeliveries;
use Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources\OrderResource\Pages\ManageInvoices;
use Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources\OrderResource\Pages\ViewOrder;
use Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources\OrderResource\Schemas\OrderForm;
use Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources\OrderResource\Schemas\OrderInfolist;
use Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources\OrderResource\Tables\OrdersTable;
use Webkul\PointOfSale\Models\Order;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-receipt-percent';

    protected static ?int $navigationSort = 1;

    protected static ?string $cluster = Orders::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getModelLabel(): string
    {
        return __('point-of-sale::models/order.title');
    }

    public static function getPluralModelLabel(): string
    {
        return __('point-of-sale::models/order.plural-title');
    }

    public static function getNavigationGroup(): ?string
    {
        return null;
    }

    public static function getNavigationLabel(): string
    {
        return __('point-of-sale::filament/admin/clusters/orders/resources/order.navigation.title');
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'reference'];
    }

    public static function infolist(Schema $schema): Schema
    {
        return OrderInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OrdersTable::configure($table);
    }

    public static function form(Schema $schema): Schema
    {
        return OrderForm::configure($schema);
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewOrder::class,
            EditOrder::class,
            ManageInvoices::class,
            ManageDeliveries::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'      => ListOrders::route('/'),
            'view'       => ViewOrder::route('/{record}'),
            'edit'       => EditOrder::route('/{record}/edit'),
            'invoices'   => ManageInvoices::route('/{record}/invoices'),
            'operations' => ManageDeliveries::route('/{record}/deliveries'),
        ];
    }
}
