<?php

namespace Webkul\Account\Filament\Resources;

use Illuminate\Database\Eloquent\Model;
use Webkul\Account\Filament\Resources\CreditNoteResource\Pages;
use Webkul\Account\Models\Move as AccountMove;

class CreditNoteResource extends InvoiceResource
{
    protected static ?string $model = AccountMove::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

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
            'index'  => Pages\ListCreditNotes::route('/'),
            'create' => Pages\CreateCreditNote::route('/create'),
            'edit'   => Pages\EditCreditNote::route('/{record}/edit'),
            'view'   => Pages\ViewCreditNote::route('/{record}'),
        ];
    }
}
