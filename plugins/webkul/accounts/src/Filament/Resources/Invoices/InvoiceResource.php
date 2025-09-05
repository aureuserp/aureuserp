<?php

namespace Webkul\Account\Filament\Resources\Invoices;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Webkul\Account\Filament\Resources\Invoices\Pages\CreateInvoice;
use Webkul\Account\Filament\Resources\Invoices\Pages\EditInvoice;
use Webkul\Account\Filament\Resources\Invoices\Pages\ListInvoices;
use Webkul\Account\Filament\Resources\Invoices\Pages\ViewInvoice;
use Webkul\Account\Filament\Resources\Invoices\Schemas\InvoiceForm;
use Webkul\Account\Filament\Resources\Invoices\Schemas\InvoiceInfolist;
use Webkul\Account\Filament\Resources\Invoices\Tables\InvoicesTable;
use Webkul\Account\Models\Move as AccountMove;

class InvoiceResource extends Resource
{
    protected static ?string $model = AccountMove::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-receipt-percent';

    protected static bool $shouldRegisterNavigation = false;

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            __('accounts::filament/resources/invoice.global-search.number')           => $record?->name ?? '—',
            __('accounts::filament/resources/invoice.global-search.customer')         => $record?->invoice_partner_display_name ?? '—',
            __('accounts::filament/resources/invoice.global-search.invoice-date')     => $record?->invoice_date ?? '—',
            __('accounts::filament/resources/invoice.global-search.invoice-date-due') => $record?->invoice_date_due ?? '—',
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return InvoiceForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return InvoicesTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return InvoiceInfolist::configure($schema);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListInvoices::route('/'),
            'create' => CreateInvoice::route('/create'),
            'view'   => ViewInvoice::route('/{record}'),
            'edit'   => EditInvoice::route('/{record}/edit'),
        ];
    }
}
