<?php

namespace Webkul\Account\Filament\Resources;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Filament\Resources\InvoiceResource\Pages\CreateInvoice;
use Webkul\Account\Filament\Resources\InvoiceResource\Pages\EditInvoice;
use Webkul\Account\Filament\Resources\InvoiceResource\Pages\ListInvoices;
use Webkul\Account\Filament\Resources\InvoiceResource\Pages\ViewInvoice;
use Webkul\Account\Filament\Resources\InvoiceResource\Schemas\InvoiceForm;
use Webkul\Account\Filament\Resources\InvoiceResource\Schemas\InvoiceInfolist;
use Webkul\Account\Filament\Resources\InvoiceResource\Tables\InvoicesTable;
use Webkul\Account\Livewire\InvoiceSummary;
use Webkul\Account\Models\Invoice;
use Webkul\Field\Filament\Traits\HasCustomFields;
use Webkul\Support\Filament\Forms\Components\Repeater;

class InvoiceResource extends Resource
{
    use HasCustomFields;

    protected static ?string $model = Invoice::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-receipt-percent';

    protected static bool $shouldRegisterNavigation = false;

    protected static bool $isGloballySearchable = false;

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            __('accounts::filament/resources/invoice.global-search.customer') => $record?->partner?->name ?? '—',
            __('accounts::filament/resources/invoice.global-search.date')     => $record?->invoice_date ?? '—',
            __('accounts::filament/resources/invoice.global-search.due-date') => $record?->invoice_date_due ?? '—',
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return InvoiceForm::configure($schema, static::class, static::getCustomFormFields());
    }

    public static function table(Table $table): Table
    {
        return InvoicesTable::configure($table, static::class, static::getCustomTableColumns(), static::getCustomTableFilters());
    }

    public static function infolist(Schema $schema): Schema
    {
        return InvoiceInfolist::configure($schema, static::class, static::getCustomInfolistEntries());
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

    public static function getProductRepeater(): Repeater
    {
        return InvoiceForm::getProductRepeater();
    }

    public static function getSummaryComponent()
    {
        return InvoiceSummary::class;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->when(Str::contains(static::class, 'InvoiceResource'), function (Builder $query) {
                $query->where('move_type', MoveType::OUT_INVOICE);
            })
            ->orderByDesc('id');
    }
}
