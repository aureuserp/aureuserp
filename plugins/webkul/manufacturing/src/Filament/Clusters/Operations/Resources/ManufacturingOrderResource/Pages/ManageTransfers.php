<?php

namespace Webkul\Manufacturing\Filament\Clusters\Operations\Resources\ManufacturingOrderResource\Pages;

use Filament\Resources\Pages\ManageRelatedRecords;
use Livewire\Livewire;
use Webkul\Manufacturing\Filament\Clusters\Operations\Resources\ManufacturingOrderResource;
use Webkul\Manufacturing\Filament\Clusters\Operations\Resources\TransferResource;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class ManageTransfers extends ManageRelatedRecords
{
    use HasRecordNavigationTabs;

    protected static string $resource = ManufacturingOrderResource::class;

    protected static string $relationship = 'inventoryOperations';

    protected static ?string $relatedResource = TransferResource::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrows-right-left';

    public static function getNavigationLabel(): string
    {
        return __('manufacturing::filament/clusters/operations/resources/manufacturing-order/pages/manage-transfers.title');
    }

    public static function getNavigationBadge($parameters = []): ?string
    {
        return (string) Livewire::current()->getRecord()->inventoryOperations()->count();
    }
}
