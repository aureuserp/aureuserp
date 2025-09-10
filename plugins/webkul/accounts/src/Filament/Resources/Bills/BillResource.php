<?php

namespace Webkul\Account\Filament\Resources\Bills;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Webkul\Account\Filament\Resources\Bills\Pages\CreateBill;
use Webkul\Account\Filament\Resources\Bills\Pages\EditBill;
use Webkul\Account\Filament\Resources\Bills\Pages\ListBills;
use Webkul\Account\Filament\Resources\Bills\Pages\ViewBill;
use Webkul\Account\Filament\Resources\Bills\Schemas\BillForm;
use Webkul\Account\Filament\Resources\Bills\Schemas\BillInfolist;
use Webkul\Account\Filament\Resources\Bills\Tables\BillsTable;
use Webkul\Account\Models\Move as AccountMove;

class BillResource extends Resource
{
    protected static ?string $model = AccountMove::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static bool $shouldRegisterNavigation = false;

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            __('accounts::filament/resources/bill.global-search.number')           => $record?->name ?? '—',
            __('accounts::filament/resources/bill.global-search.customer')         => $record?->invoice_partner_display_name ?? '—',
            __('accounts::filament/resources/bill.global-search.invoice-date')     => $record?->invoice_date ?? '—',
            __('accounts::filament/resources/bill.global-search.invoice-date-due') => $record?->invoice_date_due ?? '—',
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return BillForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BillsTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return BillInfolist::configure($schema);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListBills::route('/'),
            'create' => CreateBill::route('/create'),
            'edit'   => EditBill::route('/{record}/edit'),
            'view'   => ViewBill::route('/{record}'),
        ];
    }
}
