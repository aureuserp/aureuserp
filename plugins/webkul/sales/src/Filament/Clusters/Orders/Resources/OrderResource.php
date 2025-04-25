<?php

namespace Webkul\Sale\Filament\Clusters\Orders\Resources;

use Filament\Forms\Form;
use Filament\Infolists\Infolist;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Webkul\Sale\Enums\OrderState;
use Webkul\Sale\Filament\Clusters\Orders;
use Webkul\Sale\Filament\Clusters\Orders\Resources\OrderResource\Pages;
use Webkul\Sale\Models\Order;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $cluster = Orders::class;

    protected static ?int $navigationSort = 2;

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    public static function getModelLabel(): string
    {
        return __('sales::filament/clusters/orders/resources/order.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('sales::filament/clusters/orders/resources/order.navigation.title');
    }

    public static function form(Form $form): Form
    {
        return QuotationResource::form($form);
    }

    public static function table(Table $table): Table
    {
        return QuotationResource::table($table)
            ->modifyQueryUsing(function ($query) {
                $query->where('state', OrderState::SALE);
            });
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return QuotationResource::infolist($infolist);
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            Pages\ViewOrder::class,
            Pages\EditOrder::class,
            Pages\ManageInvoices::class,
            Pages\ManageDeliveries::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'      => Pages\ListOrders::route('/'),
            'create'     => Pages\CreateOrder::route('/create'),
            'view'       => Pages\ViewOrder::route('/{record}'),
            'edit'       => Pages\EditOrder::route('/{record}/edit'),
            'invoices'   => Pages\ManageInvoices::route('/{record}/invoices'),
            'deliveries' => Pages\ManageDeliveries::route('/{record}/deliveries'),
        ];
    }
}
