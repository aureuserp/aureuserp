<?php

namespace Webkul\Account\Filament\Resources\InvoiceResource\Actions;

use Exception;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\PaymentState;
use Webkul\Account\Enums\PaymentType;
use Webkul\Account\Facades\Account as AccountFacade;
use Webkul\Account\Models\Move;
use Webkul\Account\Models\PaymentMethodLine;
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
            ->schema(function (Schema $schema) {
                try {
                    $paymentRegister = (new PaymentRegister);

                    $paymentRegister->lines = $this->getRecord()->lines;
                    $paymentRegister->company = $this->getRecord()->company;
                    $paymentRegister->currency = $this->getRecord()->currency;
                    $paymentRegister->currency_id = $this->getRecord()->currency_id;
                    $paymentRegister->payment_type = $this->getRecord()->isInbound(true)
                        ? PaymentType::RECEIVE
                        : PaymentType::SEND;
                    $paymentRegister->computeBatches();
                    $paymentRegister->computeAvailableJournalIds();
                    $paymentRegister->journal_id = $paymentRegister->available_journal_ids[0] ?? null;
                    $paymentRegister->journal = Journal::find($paymentRegister->journal_id);

                    $paymentRegister->computePaymentMethodLineId();

                    $amountsToPay = $paymentRegister->getTotalAmountsToPay($paymentRegister->batches);
                    $paymentRegister->amount = $amountsToPay['amount_by_default'];
                    $paymentRegister->computeInstallmentsMode();
                } catch (Exception $e) {
                    Notification::make()
                        ->title(__('accounts::filament/resources/invoice/actions/pay-action.notifications.payment-failed.title'))
                        ->body($e->getMessage())
                        ->danger()
                        ->send();
                }

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
                                ->default(fn () => $paymentRegister->available_journal_ids[0] ?? null)
                                ->afterStateUpdated(function (Set $set, Get $get) use ($paymentRegister) {
                                    $paymentRegister->journal_id = $get('journal_id');
                                    $paymentRegister->journal = Journal::find($get('journal_id'));
                                    $paymentRegister->computePaymentMethodLineId();

                                    $set('payment_method_line_id', $paymentRegister->payment_method_line_id);
                                    $set('partner_bank_id', null);
                                }),

                            Select::make('payment_method_line_id')
                                ->label(__('accounts::filament/resources/invoice.form.tabs.other-information.fieldset.accounting.fields.payment-method'))
                                ->required()
                                ->searchable()
                                ->preload()
                                ->live()
                                ->default($paymentRegister->payment_method_line_id)
                                ->relationship(
                                    name: 'paymentMethodLine',
                                    titleAttribute: 'name',
                                    modifyQueryUsing: function (Builder $query, Get $get) {
                                        $journal = Journal::find($get('journal_id'));

                                        if (! $journal) {
                                            return $query->whereRaw('1 = 0');
                                        }

                                        $paymentMethodLineIds = $journal->getAvailablePaymentMethodLines(
                                            $this->getRecord()->isInbound(true)
                                                ? PaymentType::RECEIVE
                                                : PaymentType::SEND
                                        )->pluck('id');

                                        $query->whereIn('id', $paymentMethodLineIds);
                                    }
                                )
                                ->afterStateUpdated(function (Set $set, Get $get) use ($paymentRegister) {
                                    $paymentRegister->payment_method_line_id = $get('payment_method_line_id');
                                    $paymentRegister->paymentMethodLine = PaymentMethodLine::find($get('payment_method_line_id'));
                                    $paymentRegister->journal = Journal::find($get('journal_id'));
                                    $paymentRegister->computeShowRequirePartnerBank();
                                }),
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
                                ->preload()
                                ->required(function (Get $get) use ($paymentRegister) {
                                    $journal = Journal::find($get('journal_id'));

                                    if (! $journal) {
                                        return false;
                                    }

                                    $paymentRegister->journal = $journal;
                                    $paymentRegister->payment_method_line_id = $get('payment_method_line_id');
                                    $paymentRegister->paymentMethodLine = PaymentMethodLine::find($get('payment_method_line_id'));

                                    if (! $paymentRegister->paymentMethodLine) {
                                        return false;
                                    }

                                    $paymentRegister->computeShowRequirePartnerBank();

                                    return $paymentRegister->require_partner_bank_account && $paymentRegister->show_partner_bank_account;
                                })
                                ->visible(function (Get $get) use ($paymentRegister) {
                                    $journal = Journal::find($get('journal_id'));

                                    if (! $journal) {
                                        return false;
                                    }

                                    $paymentRegister->journal = $journal;
                                    $paymentRegister->payment_method_line_id = $get('payment_method_line_id');
                                    $paymentRegister->paymentMethodLine = PaymentMethodLine::find($get('payment_method_line_id'));

                                    if (! $paymentRegister->paymentMethodLine) {
                                        return false;
                                    }

                                    $paymentRegister->computeShowRequirePartnerBank();

                                    return $paymentRegister->show_partner_bank_account;
                                })
                                ->disabled(function (Get $get) use ($paymentRegister) {
                                    $journal = Journal::find($get('journal_id'));

                                    if (! $journal) {
                                        return true;
                                    }

                                    $paymentRegister->journal = $journal;
                                    $paymentRegister->payment_method_line_id = $get('payment_method_line_id');
                                    $paymentRegister->paymentMethodLine = PaymentMethodLine::find($get('payment_method_line_id'));

                                    if (! $paymentRegister->paymentMethodLine) {
                                        return true;
                                    }

                                    $paymentRegister->computeShowRequirePartnerBank();

                                    return ! $paymentRegister->require_partner_bank_account;
                                }),
                        ]),

                    Group::make()
                        ->schema([
                            Group::make()
                                ->schema([
                                    Hidden::make('installments_mode')
                                        ->default($paymentRegister->installments_mode),
                                    TextInput::make('amount')
                                        ->label(__('accounts::filament/resources/invoice/actions/pay-action.form.fields.amount'))
                                        ->prefix(fn ($record) => $record->currency->symbol ?? '')
                                        ->default($paymentRegister->amount)
                                        ->required()
                                        ->live(onBlur: true)
                                        ->afterStateUpdated(function (Set $set, $state) use ($paymentRegister) {
                                            $paymentRegister->amount = $state;
                                            $paymentRegister->computeInstallmentsMode();
                                            $set('installments_mode', $paymentRegister->installments_mode);
                                        })
                                        ->helperText(function (Get $get) use ($paymentRegister) {
                                            $paymentRegister->amount = $get('amount') ?? $paymentRegister->amount;
                                            $paymentRegister->installments_mode = $get('installments_mode') ?? $paymentRegister->installments_mode;

                                            $switchValues = $paymentRegister->computeInstallmentsSwitchValues();

                                            if (! $switchValues['installments_switch_html']) {
                                                return null;
                                            }

                                            return new HtmlString($switchValues['installments_switch_html']);
                                        })
                                        ->hintAction(
                                            Action::make('toggleInstallments')
                                                ->label(function (Get $get) use ($paymentRegister) {
                                                    $installmentsMode = $get('installments_mode') ?? $paymentRegister->installments_mode;

                                                    return $installmentsMode === 'full' ? 'installments' : 'full amount';
                                                })
                                                ->link()
                                                ->action(function (Set $set, Get $get) use ($paymentRegister) {
                                                    $switchValues = $paymentRegister->computeInstallmentsSwitchValues();

                                                    if ($switchValues['installments_switch_amount'] > 0) {
                                                        $paymentRegister->amount = $switchValues['installments_switch_amount'];
                                                        $paymentRegister->computeInstallmentsMode();

                                                        $set('amount', $paymentRegister->amount);
                                                        $set('installments_mode', $paymentRegister->installments_mode);
                                                    }
                                                })
                                        ),
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
                        ]),
                ])
                    ->columns(2);
            })
            ->action(function (Move $record, $data): void {
                $lineIds = $record->paymentTermLines
                    ->filter(fn ($line) => ! $line->reconciled)
                    ->pluck('id')
                    ->toArray();

                $paymentRegister = PaymentRegister::create($data);

                $paymentRegister->lines()->sync($lineIds);

                $paymentRegister->refresh();

                $paymentRegister->computeFromLines();

                $paymentRegister->save();

                AccountFacade::createPayments($paymentRegister);
            })
            ->hidden(function (Move $record) {
                return $record->state != MoveState::POSTED
                    || ! in_array($record->payment_state, [
                        PaymentState::NOT_PAID,
                        PaymentState::PARTIAL,
                        PaymentState::IN_PAYMENT,
                    ]);
            });
    }
}
