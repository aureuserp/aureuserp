<?php

namespace Webkul\Accounting\Filament\Clusters\Customers\Resources\CustomerResource\Pages;

use Filament\Resources\Pages\ManageRelatedRecords;
use Illuminate\Contracts\Support\Htmlable;
use Livewire\Livewire;
use Webkul\Accounting\Filament\Clusters\Customers\Resources\CustomerInvoiceResource;
use Webkul\Accounting\Filament\Clusters\Customers\Resources\CustomerResource;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class ManageInvoices extends ManageRelatedRecords
{
    use HasRecordNavigationTabs;

    protected static string $resource = CustomerResource::class;

    protected static string $relationship = 'invoices';

    protected static ?string $relatedResource = CustomerInvoiceResource::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    public static function getNavigationLabel(): string
    {
        return __('accounting::filament/clusters/customers/resources/invoice.navigation.title');
    }

    public function getTitle(): string|Htmlable
    {
        return static::getNavigationLabel();
    }

    public static function getNavigationBadge($parameters = []): ?string
    {
        return Livewire::current()->getRecord()->invoices()->count();
    }
}
