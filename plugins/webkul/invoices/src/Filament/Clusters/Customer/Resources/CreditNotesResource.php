<?php

namespace Webkul\Invoice\Filament\Clusters\Customer\Resources;

use Filament\Resources\Pages\Page;
use Webkul\Account\Filament\Resources\CreditNoteResource as BaseCreditNoteResource;
use Webkul\Invoice\Filament\Clusters\Customer;
use Webkul\Invoice\Filament\Clusters\Customer\Resources\CreditNotesResource\Pages\CreateCreditNotes;
use Webkul\Invoice\Filament\Clusters\Customer\Resources\CreditNotesResource\Pages\EditCreditNotes;
use Webkul\Invoice\Filament\Clusters\Customer\Resources\CreditNotesResource\Pages\ListCreditNotes;
use Webkul\Invoice\Filament\Clusters\Customer\Resources\CreditNotesResource\Pages\ViewCreditNote;
use Webkul\Invoice\Models\CreditNote;

class CreditNotesResource extends BaseCreditNoteResource
{
    protected static ?string $model = CreditNote::class;

    protected static bool $shouldRegisterNavigation = true;

    protected static ?string $cluster = Customer::class;

    protected static ?int $navigationSort = 2;

    public static function getModel(): string
    {
        return static::$model ?? CreditNote::class;
    }

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
