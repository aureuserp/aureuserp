<?php

namespace Webkul\Contact\Filament\Resources\PartnerResource\Pages;

use Filament\Resources\Pages\ManageRelatedRecords;
use Livewire\Livewire;
use Webkul\Contact\Filament\Resources\ContactBillResource;
use Webkul\Contact\Filament\Resources\PartnerResource;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class ManageBills extends ManageRelatedRecords
{
    use HasRecordNavigationTabs;

    protected static string $resource = PartnerResource::class;

    protected static string $relationship = 'bills';

    protected static ?string $relatedResource = ContactBillResource::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-check';

    public static function getNavigationLabel(): string
    {
        return __('contacts::filament/resources/partner.bills.navigation.title');
    }

    public static function getNavigationBadge($parameters = []): ?string
    {
        return Livewire::current()->getRecord()->bills()->count();
    }
}
