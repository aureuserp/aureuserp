<?php

namespace Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\OrderResource\Pages;

use Filament\Resources\Pages\ManageRelatedRecords;
use Livewire\Livewire;
use Webkul\PluginManager\Package;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\OrderResource;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\QuotationReceiptResource;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class ManageReceipts extends ManageRelatedRecords
{
    use HasRecordNavigationTabs;

    protected static string $resource = OrderResource::class;

    protected static string $relationship = 'operations';

    protected static ?string $relatedResource = QuotationReceiptResource::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrow-down-tray';

    /**
     * @param  array<string, mixed>  $parameters
     */
    public static function canAccess(array $parameters = []): bool
    {
        $canAccess = parent::canAccess($parameters);

        if (! $canAccess) {
            return false;
        }

        return Package::isPluginInstalled('inventories');
    }

    public static function getNavigationLabel(): string
    {
        return __('purchases::filament/admin/clusters/orders/resources/order/pages/manage-receipts.navigation.title');
    }

    public static function getNavigationBadge($parameters = []): ?string
    {
        return Livewire::current()->getRecord()->operations()->count();
    }
}
