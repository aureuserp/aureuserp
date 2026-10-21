<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources\SessionResource\Pages;

use BackedEnum;
use Filament\Resources\Pages\ManageRelatedRecords;
use Livewire\Livewire;
use Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources\PaymentResource;
use Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources\SessionResource;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class ManagePayments extends ManageRelatedRecords
{
    use HasRecordNavigationTabs;

    protected static string $resource = SessionResource::class;

    protected static string $relationship = 'payments';

    protected static ?string $relatedResource = PaymentResource::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    public static function getNavigationLabel(): string
    {
        return __('point-of-sale::filament/admin/clusters/orders/resources/session/pages/manage-payments.navigation.title');
    }

    public static function getNavigationBadge($parameters = []): ?string
    {
        return Livewire::current()->getRecord()->payments()->count();
    }
}
