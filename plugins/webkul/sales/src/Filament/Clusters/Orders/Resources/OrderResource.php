<?php

namespace Webkul\Sale\Filament\Clusters\Orders\Resources;

use BackedEnum;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Webkul\Sale\Enums\OrderState;
use Webkul\Sale\Filament\Clusters\Orders\Resources\OrderResource\Pages\CreateOrder;
use Webkul\Sale\Filament\Clusters\Orders\Resources\OrderResource\Pages\EditOrder;
use Webkul\Sale\Filament\Clusters\Orders\Resources\OrderResource\Pages\ListOrders;
use Webkul\Sale\Filament\Clusters\Orders\Resources\OrderResource\Pages\ManageDeliveries;
use Webkul\Sale\Filament\Clusters\Orders\Resources\OrderResource\Pages\ManageInvoices;
use Webkul\Sale\Filament\Clusters\Orders\Resources\OrderResource\Pages\ViewOrder;
use Webkul\Security\Traits\HasResourcePermissionQuery;

class OrderResource extends QuotationResource
{
    use HasResourcePermissionQuery;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?int $navigationSort = 2;

    public static function getModelLabel(): string
    {
        return __('sales::filament/clusters/orders/resources/order.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('sales::filament/clusters/orders/resources/order.navigation.title');
    }

    public static function getGloballySearchableAttributes(): array
    {
        return QuotationResource::getGloballySearchableAttributes();
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return QuotationResource::getGlobalSearchResultDetails($record);
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
            'create'     => CreateOrder::route('/create'),
            'view'       => ViewOrder::route('/{record}'),
            'edit'       => EditOrder::route('/{record}/edit'),
            'invoices'   => ManageInvoices::route('/{record}/invoices'),
            'deliveries' => ManageDeliveries::route('/{record}/deliveries'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        $query = static::getModel()::applyPermissionScope($query);

        return $query->where('state', OrderState::SALE);
    }
}
