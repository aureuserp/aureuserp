<?php

namespace Webkul\Account\Filament\Resources\InvoiceResource\Actions;

use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Webkul\Account\Enums\DisplayType;
use Webkul\Account\Enums\InstallmentMode;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Enums\PaymentState;
use Webkul\Account\Models\FullReconcile;
use Webkul\Account\Models\Journal;
use Webkul\Account\Models\Move;
use Webkul\Account\Models\MoveLine;
use Webkul\Account\Models\PartialReconcile;
use Webkul\Account\Models\Payment;
use Webkul\Account\Models\PaymentMethodLine;
use Webkul\Account\Models\PaymentRegister;

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
            ->color('primary')
            ->form(function (Form $form) {
                return $form->schema([
                    Forms\Components\Group::make()
                        ->schema([
                            Forms\Components\Select::make('journal_id')
                                ->relationship(
                                    'journal',
                                    'name',
                                    fn($query) => $query->whereIn('type', ['bank', 'cash'])
                                )
                                ->default(function ($record) {
                                    return Journal::where('type', 'bank')->first()->id;
                                })
                                ->searchable()
                                ->preload()
                                ->label(__('accounts::filament/resources/invoice/actions/pay-action.form.fields.journal'))
                                ->required(),
                            Forms\Components\TextInput::make('amount')
                                ->label(__('accounts::filament/resources/invoice/actions/pay-action.form.fields.amount'))
                                ->prefix(fn($record) => $record->currency->symbol ?? '')
                                ->formatStateUsing(fn($record) => number_format($record->moveLines->sum('price_total'), 2, '.', ''))
                                ->dehydrateStateUsing(fn($state) => (float) str_replace(',', '', $state))
                                ->required(),
                            Forms\Components\Select::make('payment_method_line_id')
                                ->relationship(
                                    'paymentMethodLine',
                                    'name',
                                )
                                ->label(__('accounts::filament/resources/invoice/actions/pay-action.form.fields.payment-method-line'))
                                ->default(function ($record) {
                                    return PaymentMethodLine::where('sort', 1)->first()->id;
                                })
                                ->searchable()
                                ->preload()
                                ->required(),
                            Forms\Components\DatePicker::make('payment_date')
                                ->native(false)
                                ->label(__('accounts::filament/resources/invoice/actions/pay-action.form.fields.payment-date'))
                                ->default(now())
                                ->required(),
                            Forms\Components\Select::make('partner_bank_id')
                                ->relationship(
                                    'partnerBank',
                                    'account_number',
                                )
                                ->label(__('accounts::filament/resources/invoice/actions/pay-action.form.fields.partner-bank-account'))
                                ->default(function ($record) {
                                    return $record->partner->bankAccounts->first()->id;
                                })
                                ->searchable()
                                ->required(),
                            Forms\Components\TextInput::make('communication')
                                ->label(__('accounts::filament/resources/invoice/actions/pay-action.form.fields.communication'))
                                ->default(function ($record) {
                                    return $record->name;
                                })
                                ->required(),
                        ])->columns(2),
                ]);
            })
            ->action(function (Move $record, $data): void {
                $payment = $this->createPayment($record, $data);
                $paymentRegister = $this->createPaymentRegister($record, $data);

                $this->createMove($record, $paymentRegister, $payment, $data);

                $record->update([
                    'payment_state' => PaymentState::PAID->value,
                ]);

                Notification::make()
                    ->success()
                    ->title(__('Payment created.'))
                    ->body(__('Payment has been successfully created.'))
                    ->send();
            })
            ->hidden(function (Move $record) {
                return
                    $record->state != MoveState::POSTED->value
                    || ! in_array($record->payment_state, [PaymentState::NOT_PAID->value, PaymentState::PARTIAL->value, PaymentState::IN_PAYMENT->value]);
            });
    }

    private function createPayment(Move $record, array $data)
    {
        $data = [
            ...$data,
            'move_id'                        => $record->id,
            'company_id'                     => $record->company_id,
            'partner_id'                     => $record->partner_id,
            'currency_id'                    => $record->currency_id,
            'created_by'                     => Auth::id(),
            'name'                           => $record->name,
            'state'                          => PaymentState::NOT_PAID->value,
            'payment_type'                   => $record?->paymentMethodLine?->paymentAccount?->payment_type,
            'partner_type'                   => $record?->partner?->sub_type,
            'memo'                           => $record->name,
            'payment_reference'              => $record->name,
            'date'                           => $data['payment_date'],
            'amount'                         => $data['amount'],
            'amount_company_currency_signed' => $data['amount'],
            'is_reconciled'                  => false,
            'is_matched'                     => false,
            'is_sent'                        => false,
        ];

        return Payment::create($data);
    }

    private function createPaymentRegister(Move $record, array $data)
    {
        $data = [
            ...$data,
            'currency_id'                 => $record->currency_id,
            'journal_id'                  => $data['journal_id'],
            'source_currency_id'          => $record->currency_id,
            'company_id'                  => $record->company_id,
            'partner_id'                  => $record->partner_id,
            'payment_method_line_id'      => $data['payment_method_line_id'],
            'creator_id'                  => Auth::id(),
            'payment_type'                => $record?->paymentMethodLine?->paymentAccount?->payment_type,
            'partner_type'                => $record?->partner?->sub_type,
            'payment_difference_handling' => 'open',
            'payment_date'                => $data['payment_date'],
            'amount'                      => $data['amount'],
            'source_amount'               => $data['amount'],
            'source_amount_currency'      => $data['amount'],
            'installments_mode'           => InstallmentMode::FULL->value,
        ];

        return PaymentRegister::create($data);
    }

    private function createMove(Move $record, PaymentRegister $paymentRegister, Payment $payment, array $data)
    {
        $journal = $paymentRegister->journal;

        $move = Move::create([
            'journal_id'                        => $journal->id,
            'company_id'                        => $record->company_id,
            'origin_payment_id'                 => $paymentRegister->id,
            'partner_id'                        => $record->partner_id,
            'commercial_partner_id'             => $record->partner_id,
            'partner_bank_id'                   => $data['partner_bank_id'],
            'currency_id'                       => $record->currency_id,
            'creator_id'                        => Auth::id(),
            'name'                              => $record->name,
            'reference'                         => $record->name,
            'state'                             => MoveState::POSTED->value,
            'move_type'                         => MoveType::ENTRY->value,
            'auto_post'                         => 'no',
            'payment_state'                     => PaymentState::NOT_PAID->value,
            'date'                              => $record->date,
            'invoice_date_due'                  => $record->invoice_date_due,
            'amount_total'                      => $data['amount'],
            'invoice_partner_display_name'      => $record->partner->name,
            'amount_untaxed'                    => $record?->amount_untaxed,
            'amount_tax'                        => $record?->amount_tax,
            'amount_total'                      => $record?->amount_total,
            'amount_residual'                   => $record?->amount_residual,
            'amount_untaxed_signed'             => $record?->amount_untaxed_signed,
            'amount_untaxed_in_currency_signed' => $record?->amount_untaxed_in_currency_signed,
            'amount_tax_signed'                 => $record?->amount_tax_signed,
            'amount_total_signed'               => $record?->amount_total_signed,
            'amount_total_in_currency_signed'   => $record?->amount_total_in_currency_signed,
            'amount_residual_signed'            => $record?->amount_residual_signed,
        ]);

        $debitMoveLine = MoveLine::create([
            'move_id'                  => $move->id,
            'journal_id'               => $journal->id,
            'company_id'               => $record->company_id,
            'company_currency_id'      => $record?->company?->currency_id,
            'currency_id'              => $record->currency_id,
            'account_id'               => $journal->default_account_id,
            'partner_id'               => $record->partner_id,
            'payment_id'               => $payment->id,
            'creator_id'               => Auth::id(),
            'move_name'                => $record->name,
            'parent_state'             => $record->state,
            'reference'                => $record->name,
            'name'                     => $record->name,
            'display_type'             => DisplayType::PRODUCT->value,
            'date'                     => $record->date,
            'debit'                    => $data['amount'],
            'credit'                   => 0,
            'balance'                  => $data['amount'],
            'amount_currency'          => $data['amount'],
            'amount_residual'          => $data['amount'],
            'amount_residual_currency' => $data['amount'],
            'quantity'                 => $record->moveLines->sum('quantity'),
            'price_unit'               => $record->moveLines->sum('price_unit'),
            'price_subtotal'           => $data['amount'],
            'price_total'              => $data['amount'],
        ]);

        $creditMoveLine = MoveLine::create([
            'move_id'                  => $move->id,
            'journal_id'               => $journal->id,
            'company_id'               => $record->company_id,
            'company_currency_id'      => $record?->company?->currency_id,
            'currency_id'              => $record->currency_id,
            'account_id'               => $journal->default_account_id,
            'partner_id'               => $record->partner_id,
            'payment_id'               => $payment->id,
            'creator_id'               => Auth::id(),
            'move_name'                => $record->name,
            'parent_state'             => $record->state,
            'reference'                => $record->name,
            'name'                     => $record->name,
            'display_type'             => DisplayType::PRODUCT->value,
            'debit'                    => 0,
            'credit'                   => $data['amount'],
            'balance'                  => -$data['amount'],
            'amount_currency'          => -$data['amount'],
            'amount_residual'          => -$data['amount'],
            'amount_residual_currency' => -$data['amount'],
            'quantity'                 => $record->moveLines->sum('quantity'),
            'price_unit'               => $record->moveLines->sum('price_unit'),
            'price_subtotal'           => -$data['amount'],
            'price_total'              => -$data['amount'],
        ]);

        $fullReconcile = FullReconcile::create([
            'exchange_move_id' => null,
            'created_id'       => Auth::id(),
        ]);

        PartialReconcile::create([
            'debit_move_id'          => $debitMoveLine->id,
            'credit_move_id'         => $creditMoveLine->id,
            'full_reconcile_id'      => $fullReconcile->id,
            'debit_currency_id'      => $debitMoveLine->currency_id,
            'credit_currency_id'     => $creditMoveLine->currency_id,
            'company_id'             => $record->company_id,
            'amount'                 => $data['amount'],
            'debit_amount_currency'  => $data['amount'],
            'credit_amount_currency' => $data['amount'],
            'creator_id'             => Auth::id(),
        ]);
    }
}
