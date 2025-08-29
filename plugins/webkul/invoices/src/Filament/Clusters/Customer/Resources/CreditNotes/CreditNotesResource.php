<?php

namespace Webkul\Invoice\Filament\Clusters\Customer\Resources\CreditNotes;

use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Resources\Pages\Page;
use Webkul\Account\Filament\Resources\CreditNotes\CreditNoteResource as BaseCreditNoteResource;
use Webkul\Invoice\Filament\Clusters\Customer;
use Webkul\Invoice\Filament\Clusters\Customer\Resources\CreditNotes\Pages\CreateCreditNotes;
use Webkul\Invoice\Filament\Clusters\Customer\Resources\CreditNotes\Pages\EditCreditNotes;
use Webkul\Invoice\Filament\Clusters\Customer\Resources\CreditNotes\Pages\ListCreditNotes;
use Webkul\Invoice\Filament\Clusters\Customer\Resources\CreditNotes\Pages\ViewCreditNote;
use Webkul\Invoice\Models\CreditNote;

class CreditNotesResource extends BaseCreditNoteResource
{
    protected static ?string $model = CreditNote::class;

    protected static bool $shouldRegisterNavigation = true;

    protected static ?string $cluster = Customer::class;

    protected static ?int $navigationSort = 2;

    protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    public static function getModelLabel(): string
    {
        return __('invoices::filament/clusters/customers/resources/credit-note.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('invoices::filament/clusters/customers/resources/credit-note.navigation.title');
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewCreditNote::class,
            EditCreditNotes::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListCreditNotes::route('/'),
            'create' => CreateCreditNotes::route('/create'),
            'edit'   => EditCreditNotes::route('/{record}/edit'),
            'view'   => ViewCreditNote::route('/{record}'),
        ];
    }
}
