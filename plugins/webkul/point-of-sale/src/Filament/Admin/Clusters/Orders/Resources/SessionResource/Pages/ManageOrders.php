<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources\SessionResource\Pages;

use BackedEnum;
use Filament\Resources\Pages\ManageRelatedRecords;
use Livewire\Livewire;
use Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources\OrderResource;
use Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources\SessionResource;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class ManageOrders extends ManageRelatedRecords
{
    use HasRecordNavigationTabs;

    protected static string $resource = SessionResource::class;

    protected static string $relationship = 'orders';

    protected static ?string $relatedResource = OrderResource::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-shopping-bag';

    public static function getNavigationLabel(): string
    {
        return __('point-of-sale::filament/admin/clusters/orders/resources/session/pages/manage-orders.navigation.title');
    }

    public static function getNavigationBadge($parameters = []): ?string
    {
        return Livewire::current()->getRecord()->orders()->count();
    }
}
