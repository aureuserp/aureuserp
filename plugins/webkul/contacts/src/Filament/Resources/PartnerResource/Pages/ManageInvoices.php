<?php

namespace Webkul\Contact\Filament\Resources\PartnerResource\Pages;

use Filament\Resources\Pages\ManageRelatedRecords;
use Livewire\Livewire;
use Webkul\Contact\Filament\Resources\ContactInvoiceResource;
use Webkul\Contact\Filament\Resources\PartnerResource;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class ManageInvoices extends ManageRelatedRecords
{
    use HasRecordNavigationTabs;

    protected static string $resource = PartnerResource::class;

    protected static string $relationship = 'invoices';

    protected static ?string $relatedResource = ContactInvoiceResource::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    public static function getNavigationLabel(): string
    {
        return __('contacts::filament/resources/partner.invoices.navigation.title');
    }

    public static function getNavigationBadge($parameters = []): ?string
    {
        return Livewire::current()->getRecord()->invoices()->count();
    }
}
