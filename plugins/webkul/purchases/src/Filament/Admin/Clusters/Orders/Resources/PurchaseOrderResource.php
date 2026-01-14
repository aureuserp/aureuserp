<?php

namespace Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources;

use Filament\Resources\Pages\Page;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Purchase\Enums\OrderState;
use Webkul\Purchase\Filament\Admin\Clusters\Orders;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\PurchaseOrderResource\Pages\CreatePurchaseOrder;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\PurchaseOrderResource\Pages\EditPurchaseOrder;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\PurchaseOrderResource\Pages\ListPurchaseOrders;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\PurchaseOrderResource\Pages\ManageBills;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\PurchaseOrderResource\Pages\ManageReceipts;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\PurchaseOrderResource\Pages\ViewPurchaseOrder;
use Webkul\Purchase\Models\PurchaseOrder;

class PurchaseOrderResource extends OrderResource
{
    protected static ?string $model = PurchaseOrder::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-check';

    protected static bool $shouldRegisterNavigation = true;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 2;

    protected static ?string $cluster = Orders::class;

    public static function getNavigationLabel(): string
    {
        return __('purchases::filament/admin/clusters/orders/resources/purchase-order.navigation.title');
    }

    public static function getModelLabel(): string
    {
        return __('purchases::filament/admin/clusters/orders/resources/purchase-order.navigation.title');
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewPurchaseOrder::class,
            EditPurchaseOrder::class,
            ManageBills::class,
            ManageReceipts::class,
        ]);
    }

    public static function table(Table $table): Table
    {
        return parent::table($table)
            ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('state', [OrderState::PURCHASE, OrderState::DONE]));
    }

    public static function getPages(): array
    {
        return [
            'index'    => ListPurchaseOrders::route('/'),
            'create'   => CreatePurchaseOrder::route('/create'),
            'view'     => ViewPurchaseOrder::route('/{record}'),
            'edit'     => EditPurchaseOrder::route('/{record}/edit'),
            'bills'    => ManageBills::route('/{record}/bills'),
            'receipts' => ManageReceipts::route('/{record}/receipts'),
        ];
    }
}
