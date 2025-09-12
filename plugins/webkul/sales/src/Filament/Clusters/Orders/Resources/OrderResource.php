<?php

namespace Webkul\Sale\Filament\Clusters\Orders\Resources;

use Filament\Resources\Pages\Page;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Sale\Enums\OrderState;
use Webkul\Sale\Filament\Clusters\Orders\Resources\OrderResource\Pages;
use Webkul\Security\Traits\HasResourcePermissionQuery;

class OrderResource extends QuotationResource
{
    use HasResourcePermissionQuery;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?int $navigationSort = 2;

    public static function getModelLabel(): string
    {
        return __('sales::filament/clusters/orders/resources/order.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('sales::filament/clusters/orders/resources/order.navigation.title');
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        $query = static::getModel()::applyPermissionScope($query);

        return $query->where('state', OrderState::SALE);
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
