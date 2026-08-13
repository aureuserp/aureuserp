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
use Webkul\Account\Filament\Resources\BillResource\Pages\CreateBill;
use Webkul\Account\Filament\Resources\BillResource\Pages\EditBill;
use Webkul\Account\Filament\Resources\BillResource\Pages\ListBills;
use Webkul\Account\Filament\Resources\BillResource\Pages\ViewBill;
use Webkul\Account\Filament\Resources\BillResource\Schemas\BillForm;
use Webkul\Account\Filament\Resources\BillResource\Schemas\BillInfolist;
use Webkul\Account\Filament\Resources\BillResource\Tables\BillsTable;
use Webkul\Account\Livewire\InvoiceSummary;
use Webkul\Account\Models\Bill;
use Webkul\Field\Filament\Traits\HasCustomFields;
use Webkul\Support\Filament\Forms\Components\Repeater;

class BillResource extends Resource
{
    use HasCustomFields;

    protected static ?string $model = Bill::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static bool $shouldRegisterNavigation = false;

    protected static bool $isGloballySearchable = false;

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            __('accounts::filament/resources/bill.global-search.vendor')   => $record->partner?->name ?? '—',
            __('accounts::filament/resources/bill.global-search.date')     => $record?->invoice_date ?? '—',
            __('accounts::filament/resources/bill.global-search.due-date') => $record?->invoice_date_due ?? '—',
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return BillForm::configure($schema, static::class, static::getCustomFormFields());
    }

    public static function table(Table $table): Table
    {
        return BillsTable::configure($table, static::class, static::getCustomTableColumns(), static::getCustomTableFilters());
    }

    public static function infolist(Schema $schema): Schema
    {
        return BillInfolist::configure($schema, static::class, static::getCustomInfolistEntries());
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

    public static function getProductRepeater(): Repeater
    {
        return BillForm::getProductRepeater();
    }

    public static function getSummaryComponent()
    {
        return InvoiceSummary::class;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->when(Str::contains(static::class, 'BillResource'), function (Builder $query) {
                $query->where('move_type', MoveType::IN_INVOICE);
            })
            ->orderByDesc('id');
    }
}
