<?php

namespace Webkul\Account\Filament\Resources\CreditNotes;

use BackedEnum;
use Illuminate\Database\Eloquent\Model;
use Webkul\Account\Filament\Resources\CreditNotes\Pages\CreateCreditNote;
use Webkul\Account\Filament\Resources\CreditNotes\Pages\EditCreditNote;
use Webkul\Account\Filament\Resources\CreditNotes\Pages\ListCreditNotes;
use Webkul\Account\Filament\Resources\CreditNotes\Pages\ViewCreditNote;
use Webkul\Account\Filament\Resources\Invoices\InvoiceResource;
use Webkul\Account\Models\Move as AccountMove;

class CreditNoteResource extends InvoiceResource
{
    protected static ?string $model = AccountMove::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-credit-card';

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            __('accounts::filament/resources/credit-note.global-search.number')           => $record?->name ?? '—',
            __('accounts::filament/resources/credit-note.global-search.customer')         => $record?->invoice_partner_display_name ?? '—',
            __('accounts::filament/resources/credit-note.global-search.invoice-date')     => $record?->invoice_date ?? '—',
            __('accounts::filament/resources/credit-note.global-search.invoice-date-due') => $record?->invoice_date_due ?? '—',
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListCreditNotes::route('/'),
            'create' => CreateCreditNote::route('/create'),
            'edit'   => EditCreditNote::route('/{record}/edit'),
            'view'   => ViewCreditNote::route('/{record}'),
        ];
    }
}
