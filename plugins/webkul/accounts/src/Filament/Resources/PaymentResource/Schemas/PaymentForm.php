<?php

namespace Webkul\Account\Filament\Resources\PaymentResource\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Account\Enums\JournalType;
use Webkul\Account\Enums\PaymentStatus;
use Webkul\Account\Enums\PaymentType;
use Webkul\Account\Models\Journal;
use Webkul\Account\Models\Partner;
use Webkul\Account\Models\Payment;
use Webkul\Account\Models\PaymentMethodLine;
use Webkul\Field\Filament\Forms\Components\ProgressStepper as FormProgressStepper;

class PaymentForm
{
    public static function configure(Schema $schema, array $customFormFields = []): Schema
    {
        return $schema
            ->disabled(fn ($record) => $record && $record->state !== PaymentStatus::DRAFT)
            ->components([
                FormProgressStepper::make('state')
                    ->hiddenLabel()
                    ->inline()
                    ->options(function ($record) {
                        $options = PaymentStatus::options();

                        if (
                            $record
                            && $record->state != PaymentStatus::NOT_PAID
                        ) {
                            unset($options[PaymentStatus::NOT_PAID->value]);
                        }

                        if (
                            $record
                            && $record->state != PaymentStatus::CANCELED
                        ) {
                            unset($options[PaymentStatus::CANCELED->value]);
                        }

                        if (
                            $record
                            && $record->state != PaymentStatus::REJECTED
                        ) {
                            unset($options[PaymentStatus::REJECTED->value]);
                        }

                        if ($record == null) {
                            unset($options[PaymentStatus::CANCELED->value]);
                        }

                        return $options;
                    })
                    ->default(PaymentStatus::DRAFT->value)
                    ->columnSpan('full')
                    ->disabled()
                    ->live()
                    ->reactive(),

                Section::make()
                    ->schema([
                        Group::make()
                            ->schema([
                                ToggleButtons::make('payment_type')
                                    ->label(__('accounts::filament/resources/payment.form.sections.fields.payment-type'))
                                    ->options(PaymentType::class)
                                    ->inline(true)
                                    ->live(),
                            ]),

                        Group::make()
                            ->schema([
                                Group::make()
                                    ->schema([
                                        Select::make('partner_id')
                                            ->label(
                                                fn (Get $get) => $get('payment_type') === PaymentType::RECEIVE
                                                    ? __('accounts::filament/resources/payment.form.sections.fields.customer')
                                                    : __('accounts::filament/resources/payment.form.sections.fields.vendor')
                                            )
                                            ->relationship(
                                                'partner',
                                                'name',
                                                fn (Builder $query, Get $get) => $query->orderBy('id'),
                                            )
                                            ->reactive()
                                            ->afterStateUpdated(function (Set $set, $state) {
                                                $partner = $state ? Partner::find($state) : null;

                                                $set('partner_bank_id', $partner?->bankAccounts->first()?->id);
                                                $set('payment_method_line_id', $partner?->propertyInboundPaymentMethodLine?->id);
                                            })
                                            ->searchable()
                                            ->preload(),

                                        Group::make()
                                            ->schema([
                                                TextInput::make('amount')
                                                    ->label(__('accounts::filament/resources/payment.form.sections.fields.amount'))
                                                    ->default(0)
                                                    ->numeric()
                                                    ->minValue(0)
                                                    ->maxValue(99999999999)
                                                    ->required(),
                                                Select::make('currency_id')
                                                    ->label(__('accounts::filament/resources/payment.form.sections.fields.currency'))
                                                    ->relationship(
                                                        name: 'currency',
                                                        titleAttribute: 'name',
                                                        modifyQueryUsing: fn (Builder $query) => $query->active(),
                                                    )
                                                    ->required()
                                                    ->searchable()
                                                    ->preload()
                                                    ->default(fn () => current_company()?->currency_id),
                                            ])
                                            ->columns(2),

                                        DatePicker::make('date')
                                            ->label(__('accounts::filament/resources/payment.form.sections.fields.date'))
                                            ->native(false)
                                            ->default(now())
                                            ->required(),
                                        TextInput::make('memo')
                                            ->label(__('accounts::filament/resources/payment.form.sections.fields.memo'))
                                            ->maxLength(255),
                                    ])
                                    ->columns(1),

                                Group::make()
                                    ->schema([
                                        Select::make('journal_id')
                                            ->label(__('accounts::filament/resources/payment.form.sections.fields.journal'))
                                            ->relationship(
                                                'journal',
                                                'name',
                                                modifyQueryUsing: fn (Builder $query) => $query->whereIn('type', [JournalType::BANK, JournalType::CASH, JournalType::CREDIT_CARD]),
                                            )
                                            ->default(function () {
                                                $journal = Journal::whereIn('type', [JournalType::BANK, JournalType::CASH, JournalType::CREDIT_CARD])->first();

                                                return $journal?->id;
                                            })
                                            ->afterStateUpdated(function (Set $set, Get $get) {
                                                $payment = new Payment;
                                                $payment->payment_type = $get('payment_type');
                                                $payment->journal_id = $get('journal_id');
                                                $payment->journal = Journal::find($get('journal_id'));
                                                $payment->computePaymentMethodLineId();

                                                $set('payment_method_line_id', $payment->payment_method_line_id);
                                                $set('partner_bank_id', null);
                                            })
                                            ->required()
                                            ->searchable()
                                            ->preload()
                                            ->live(),
                                        Select::make('payment_method_line_id')
                                            ->label(__('accounts::filament/resources/payment.form.sections.fields.payment-method'))
                                            ->relationship(
                                                name: 'paymentMethodLine',
                                                titleAttribute: 'name',
                                                modifyQueryUsing: function (Builder $query, Get $get) {
                                                    $journal = Journal::find($get('journal_id'));

                                                    if (! $journal) {
                                                        return $query->whereRaw('1 = 0');
                                                    }

                                                    $paymentMethodLineIds = $journal->getAvailablePaymentMethodLines($get('payment_type'))->pluck('id');

                                                    $query->whereIn('id', $paymentMethodLineIds);
                                                }
                                            )
                                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->display_name)
                                            ->required()
                                            ->searchable()
                                            ->preload()
                                            ->live(),
                                        Select::make('partner_bank_id')
                                            ->label(
                                                fn (Get $get) => $get('payment_type') === PaymentType::RECEIVE
                                                    ? __('accounts::filament/resources/payment.form.sections.fields.customer-bank-account')
                                                    : __('accounts::filament/resources/payment.form.sections.fields.vendor-bank-account')
                                            )
                                            ->relationship(
                                                'partnerBank',
                                                'account_number',
                                                modifyQueryUsing: fn (Builder $query, Get $get) => $query->withTrashed()->where('partner_id', $get('partner_id')),
                                            )
                                            ->getOptionLabelFromRecordUsing(function ($record): string {
                                                return $record->account_number.' - '.$record->bank->name.($record->trashed() ? ' (Deleted)' : '');
                                            })
                                            ->disableOptionWhen(function ($label) {
                                                return str_contains($label, ' (Deleted)');
                                            })
                                            ->required(function (Get $get) {
                                                return static::computePayment($get)->require_partner_bank_account
                                                    && static::computePayment($get)->show_partner_bank_account;
                                            })
                                            ->visible(function (Get $get) {
                                                return static::computePayment($get)->show_partner_bank_account;
                                            })
                                            ->disabled(function (Get $get) {
                                                return static::computePayment($get)->require_partner_bank_account;
                                            })
                                            ->searchable()
                                            ->preload(),
                                    ])
                                    ->columns(1),
                            ])
                            ->columns(2),

                        ...$customFormFields,
                    ])
                    ->columns(1),
            ])
            ->columns(1);
    }

    private static function computePayment($get)
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
}
