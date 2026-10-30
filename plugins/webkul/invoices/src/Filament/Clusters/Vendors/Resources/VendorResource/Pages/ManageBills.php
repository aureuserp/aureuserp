<?php

namespace Webkul\Invoice\Filament\Clusters\Vendors\Resources\VendorResource\Pages;

use Filament\Resources\Pages\ManageRelatedRecords;
use Livewire\Livewire;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\VendorBillResource;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\VendorResource;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class ManageBills extends ManageRelatedRecords
{
    use HasRecordNavigationTabs;

    protected static string $resource = VendorResource::class;

    protected static string $relationship = 'bills';

    protected static ?string $relatedResource = VendorBillResource::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-check';

    public static function getNavigationLabel(): string
    {
        return __('invoices::filament/clusters/vendors/resources/bill.navigation.title');
    }

    public static function getNavigationBadge($parameters = []): ?string
    {
        return Livewire::current()->getRecord()->bills()->count();
    }
}
