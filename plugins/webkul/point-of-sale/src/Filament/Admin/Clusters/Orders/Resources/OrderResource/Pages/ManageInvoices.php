<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources\OrderResource\Pages;

use BackedEnum;
use Filament\Resources\Pages\ManageRelatedRecords;
use Livewire\Livewire;
use Webkul\PluginManager\Package;
use Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources\OrderInvoiceResource;
use Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources\OrderResource;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class ManageInvoices extends ManageRelatedRecords
{
    use HasRecordNavigationTabs;

    protected static string $resource = OrderResource::class;

    protected static string $relationship = 'invoices';

    protected static ?string $relatedResource = OrderInvoiceResource::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    public static function canAccess(array $parameters = []): bool
    {
        return parent::canAccess($parameters) && Package::isPluginInstalled('invoices');
    }

    public static function getNavigationLabel(): string
    {
        return __('point-of-sale::filament/admin/clusters/orders/resources/order/pages/manage-invoices.navigation.title');
    }

    public static function getNavigationBadge($parameters = []): ?string
    {
        return Livewire::current()->getRecord()->invoices()->count();
    }
}
