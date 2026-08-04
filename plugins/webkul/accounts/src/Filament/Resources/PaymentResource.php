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
use Webkul\Account\Models\Journal;
use Webkul\Account\Models\Partner;
use Webkul\Account\Models\Payment;
use Webkul\Account\Models\PaymentMethodLine;
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
        return PaymentForm::configure($schema, static::class, static::getCustomFormFields());
    }

    public static function table(Table $table): Table
    {
        return PaymentsTable::configure($table, static::class, static::getCustomTableColumns(), static::getCustomTableFilters());
    }

    public static function infolist(Schema $schema): Schema
    {
        return PaymentInfolist::configure($schema, static::class, static::getCustomInfolistEntries());
    }

    public static function computePayment($get)
    {
        $journal = Journal::find($get('journal_id'));

        $payment = new Payment;

        if (! $journal) {
            return $payment;
        }

        $payment->payment_type = $get('payment_type');
        $payment->journal = $journal;
        $payment->payment_method_line_id = $get('payment_method_line_id');
        $payment->paymentMethodLine = PaymentMethodLine::find($get('payment_method_line_id'));
        $payment->partner_id = $get('partner_id');
        $payment->partner = Partner::find($get('partner_id'));

        if (! $payment->paymentMethodLine) {
            $payment->computePaymentMethodLineId();

            return $payment;
        }

        if (! $payment->paymentMethodLine) {
            return $payment;
        }

        $payment->computeShowRequirePartnerBank();

        return $payment;
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
