<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources\SessionResource\Pages;

use BackedEnum;
use Filament\Resources\Pages\ManageRelatedRecords;
use Livewire\Livewire;
use Webkul\PluginManager\Package;
use Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources\OrderDeliveryResource;
use Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources\SessionResource;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class ManagePickings extends ManageRelatedRecords
{
    use HasRecordNavigationTabs;

    protected static string $resource = SessionResource::class;

    protected static string $relationship = 'deliveries';

    protected static ?string $relatedResource = OrderDeliveryResource::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-truck';

    public static function canAccess(array $parameters = []): bool
    {
        return parent::canAccess($parameters) && Package::isPluginInstalled('inventories');
    }

    public static function getNavigationLabel(): string
    {
        return __('point-of-sale::filament/admin/clusters/orders/resources/session/pages/manage-pickings.navigation.title');
    }

    public static function getNavigationBadge($parameters = []): ?string
    {
        return Livewire::current()->getRecord()->deliveries()->count();
    }
}
