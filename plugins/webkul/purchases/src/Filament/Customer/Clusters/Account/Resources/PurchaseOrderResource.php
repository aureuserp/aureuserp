<?php

namespace Webkul\Purchase\Filament\Customer\Clusters\Account\Resources;

use Webkul\Purchase\Filament\Customer\Clusters\Account\Resources\PurchaseOrderResource\Pages\ListPurchaseOrders;
use Webkul\Purchase\Filament\Customer\Clusters\Account\Resources\PurchaseOrderResource\Pages\ViewPurchaseOrder;
use Webkul\Purchase\Models\CustomerPurchaseOrder as PurchaseOrder;

class PurchaseOrderResource extends OrderResource
{
    protected static ?string $model = PurchaseOrder::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?int $navigationSort = 2;

    protected static bool $shouldRegisterNavigation = true;

    public static function getNavigationLabel(): string
    {
        return __('purchases::filament/customer/clusters/account/resources/purchase-order.navigation.title');
    }

    public static function getModelLabel(): string
    {
        return __('purchases::filament/customer/clusters/account/resources/purchase-order.navigation.title');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPurchaseOrders::route('/'),
            'view'  => ViewPurchaseOrder::route('/{record}'),
        ];
    }
}
