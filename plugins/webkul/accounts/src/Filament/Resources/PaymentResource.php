<?php

namespace Webkul\Account\Filament\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Webkul\Account\Filament\Resources\PaymentResource\Pages\CreatePayment;
use Webkul\Account\Filament\Resources\PaymentResource\Pages\EditPayment;
use Webkul\Account\Filament\Resources\PaymentResource\Pages\ListPayments;
use Webkul\Account\Filament\Resources\PaymentResource\Pages\ViewPayment;
use Webkul\Account\Filament\Resources\PaymentResource\Schemas\PaymentForm;
use Webkul\Account\Filament\Resources\PaymentResource\Schemas\PaymentInfolist;
use Webkul\Account\Filament\Resources\PaymentResource\Tables\PaymentsTable;
use Webkul\Account\Models\Payment;
use Webkul\Field\Filament\Traits\HasCustomFields;

class PaymentResource extends Resource
{
    use HasCustomFields;

    protected static ?string $model = Payment::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    protected static bool $shouldRegisterNavigation = false;

    protected static bool $isGloballySearchable = false;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'partner.name', 'amount'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            __('accounts::filament/resources/payment.global-search.partner') => $record->partner?->name ?? '—',
            __('accounts::filament/resources/payment.global-search.amount')  => $record->amount ? money($record->amount) : '—',
            __('accounts::filament/resources/payment.global-search.date')    => $record->date ?? '—',
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return PaymentForm::configure($schema, static::getCustomFormFields());
    }

    public static function table(Table $table): Table
    {
        return PaymentsTable::configure($table, static::getCustomTableColumns(), static::getCustomTableFilters());
    }

    public static function infolist(Schema $schema): Schema
    {
        return PaymentInfolist::configure($schema, static::getCustomInfolistEntries());
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListPayments::route('/'),
            'create' => CreatePayment::route('/create'),
            'view'   => ViewPayment::route('/{record}'),
            'edit'   => EditPayment::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->orderByDesc('id');
    }
}
