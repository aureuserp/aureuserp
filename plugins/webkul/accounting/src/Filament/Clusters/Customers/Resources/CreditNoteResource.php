<?php

namespace Webkul\Accounting\Filament\Clusters\Customers\Resources;

use Filament\Resources\Pages\Page;
use Webkul\Account\Filament\Resources\CreditNoteResource as BaseCreditNoteResource;
use Webkul\Accounting\Filament\Clusters\Customers;
use Webkul\Accounting\Filament\Clusters\Customers\Resources\CreditNoteResource\Pages\CreateCreditNote;
use Webkul\Accounting\Filament\Clusters\Customers\Resources\CreditNoteResource\Pages\EditCreditNote;
use Webkul\Accounting\Filament\Clusters\Customers\Resources\CreditNoteResource\Pages\ListCreditNotes;
use Webkul\Accounting\Filament\Clusters\Customers\Resources\CreditNoteResource\Pages\ManagePayments;
use Webkul\Accounting\Filament\Clusters\Customers\Resources\CreditNoteResource\Pages\ViewCreditNote;
use Webkul\Accounting\Livewire\InvoiceSummary;
use Webkul\Accounting\Models\CreditNote;

class CreditNoteResource extends BaseCreditNoteResource
{
    protected static ?string $model = CreditNote::class;

    protected static bool $shouldRegisterNavigation = true;

    protected static ?string $cluster = Customers::class;

    protected static ?int $navigationSort = 2;

    public static function getModel(): string
    {
        return static::$model ?? CreditNote::class;
    }

    public static function getModelLabel(): string
    {
        return __('accounting::filament/clusters/customers/resources/credit-note.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('accounting::filament/clusters/customers/resources/credit-note.navigation.title');
    }

    public static function getSummaryComponent()
    {
        return InvoiceSummary::class;
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewCreditNote::class,
            EditCreditNote::class,
            ManagePayments::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'    => ListCreditNotes::route('/'),
            'create'   => CreateCreditNote::route('/create'),
            'edit'     => EditCreditNote::route('/{record}/edit'),
            'view'     => ViewCreditNote::route('/{record}'),
            'payments' => ManagePayments::route('/{record}/payments'),
        ];
    }
}
