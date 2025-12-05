<?php

namespace Webkul\Account\Filament\Resources\InvoiceResource\Actions;

use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\PaymentState;
use Webkul\Account\Models\Move;
use Webkul\Account\Models\PaymentRegister;
use Webkul\Accounting\Models\Journal;

class PayAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'customers.invoice.pay';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('accounts::filament/resources/invoice/actions/pay-action.title'))
            ->color('success')
            ->modal(fn () => new PaymentRegister())
            ->schema(function (Schema $schema) {
                $paymentRegister = (new PaymentRegister);

                $paymentRegister->lines = $this->getRecord()->lines;
                $paymentRegister->company = $this->getRecord()->company;
                $paymentRegister->currency = $this->getRecord()->currency;
                $paymentRegister->payment_type = $this->getRecord()->isSaleDocument(true)
                    ? 'inbound'
                    : 'outbound';
                $paymentRegister->computeBatches();
                $paymentRegister->computeAvailableJournalIds();

                return $schema->components([
                    Group::make()
                        ->schema([
                            Select::make('journal_id')
                                ->relationship(
                                    'journal',
                                    'name',
                                    modifyQueryUsing: fn (Builder $query) => $query->whereIn('id', $paymentRegister->available_journal_ids)
                                )
                                ->label(__('accounts::filament/resources/invoice/actions/pay-action.form.fields.journal'))
                                ->searchable()
                                ->preload()
                                ->required()
                                ->live()
                                ->default(fn() => $paymentRegister->available_journal_ids[0] ?? null)
                                ->afterStateUpdated(function (Set $set, Get $get) {
                                    $set('partner_bank_id', null);
                                }),

                            Select::make('payment_method_line_id')
                                ->label('Payment Method')
                                ->required()
                                ->searchable()
                                ->preload()
                                ->relationship(
                                    name: 'paymentMethodLine',
                                    titleAttribute: 'name',
                                    modifyQueryUsing: function (Builder $query, Get $get) {
                                        $journal = Journal::find($get('journal_id'));

                                        if (! $journal) {
                                            return $query->whereRaw('1 = 0');
                                        }

                                        $paymentMethodLineIds = $journal->getAvailablePaymentMethodLines(
                                            $this->getRecord()->isSaleDocument(true)
                                                ? 'inbound'
                                                : 'outbound'
                                        )->pluck('id');

                                        $query->whereIn('id', $paymentMethodLineIds);
                                    }
                                ),
                            Select::make('partner_bank_id')
                                ->relationship(
                                    'partnerBank',
                                    'account_number',
                                    modifyQueryUsing: fn (Builder $query, $record) => $query->where('partner_id', $record->partner_id)->withTrashed(),
                                )
                                ->getOptionLabelFromRecordUsing(function ($record): string {
                                    return $record->account_number.' - '.$record->bank->name.($record->trashed() ? ' (Deleted)' : '');
                                })
                                ->disableOptionWhen(function ($label) {
                                    return str_contains($label, ' (Deleted)');
                                })
                                ->label(__('accounts::filament/resources/invoice/actions/pay-action.form.fields.partner-bank-account'))
                                ->searchable()
                                ->required()
                                ->preload()
                                ->visible(function (Get $get) use ($paymentRegister) {
                                    $journal = Journal::find($get('journal_id'));

                                    if (! $journal) {
                                        return false;
                                    }

                                    $paymentRegister->journal = $journal;
                                    $paymentRegister->computePaymentMethodLineId();
                                    $paymentRegister->computeShowRequirePartnerBank();

                                    return $paymentRegister->show_partner_bank_account;
                                })
                                ->disabled(function (Get $get) use ($paymentRegister) {
                                    $journal = Journal::find($get('journal_id'));

                                    if (! $journal) {
                                        return true;
                                    }

                                    $paymentRegister->journal = $journal;
                                    $paymentRegister->computeShowRequirePartnerBank();

                                    return ! $paymentRegister->require_partner_bank_account;

                                }),
                        ]),

                    Group::make()
                        ->schema([
                            Group::make()
                                ->schema([
                                    TextInput::make('amount')
                                        ->label(__('accounts::filament/resources/invoice/actions/pay-action.form.fields.amount'))
                                        ->prefix(fn($record) => $record->currency->symbol ?? '')
                                        // ->formatStateUsing(fn($record) => number_format($record->lines->sum('price_total'), 2, '.', ''))
                                        // ->dehydrateStateUsing(fn($state) => (float) str_replace(',', '', $state))
                                        ->default(function ($record) use($paymentRegister) {
                                            $result = $paymentRegister->getTotalAmountsToPay($paymentRegister->batches);

                                            dd($result);
                                        })
                                        ->required(),
                                    Select::make('currency_id')
                                        ->label(__('accounts::filament/resources/invoice/actions/pay-action.form.fields.currency'))
                                        ->relationship(
                                            'currency',
                                            'name',
                                            modifyQueryUsing: fn (Builder $query) => $query->where('active', 1),
                                        )
                                        ->default(function ($record, Get $get) {
                                            $journal = Journal::find($get('journal_id'));

                                            if (! $journal) {
                                                return $record->currency_id;
                                            }

                                            return $journal->currency_id ?? $record->currency_id;
                                        })
                                        ->required()
                                        ->searchable()
                                        ->preload()
                                        ->required(),
                                ])
                                ->columns(2),
                            DatePicker::make('payment_date')
                                ->native(false)
                                ->label(__('accounts::filament/resources/invoice/actions/pay-action.form.fields.payment-date'))
                                ->default(now())
                                ->required(),
                            TextInput::make('communication')
                                ->label(__('accounts::filament/resources/invoice/actions/pay-action.form.fields.communication'))
                                ->default(function ($record) {
                                    return $record->name;
                                })
                                ->required(),
                        ])
                ])
                ->columns(2);
            })
            ->action(function (Move $record, $data): void {
                dd($data);
            })
            ->hidden(function (Move $record) {
                return $record->state != MoveState::POSTED
                    || ! in_array($record->payment_state, [
                        PaymentState::NOT_PAID,
                        PaymentState::PARTIAL,
                        PaymentState::IN_PAYMENT
                    ]);
            });
    }
}
