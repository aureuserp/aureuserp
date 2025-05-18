<?php

namespace Webkul\Account\Filament\Resources\InvoiceResource\Actions;

use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Form;
use Illuminate\Support\Facades\Auth;
use Webkul\Account\Enums\DisplayType;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Enums\PaymentState;
use Webkul\Account\Models\Move;
use Webkul\Account\Models\MoveLine;
use Webkul\Account\Models\Payment;
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
            ->color('success')
            ->form(function (Form $form) {
                return $form->schema([
                    Forms\Components\Group::make()
                        ->schema([
                            Forms\Components\TextInput::make('amount')
                                ->label(__('accounts::filament/resources/invoice/actions/pay-action.form.fields.amount'))
                                ->prefix(fn ($record) => $record->currency->symbol ?? '')
                                ->formatStateUsing(fn ($record) => number_format($record->lines->sum('price_total'), 2, '.', ''))
                                ->dehydrateStateUsing(fn ($state) => (float) str_replace(',', '', $state))
                                ->required(),
                            Forms\Components\Select::make('payment_method_line_id')
                                ->relationship(
                                    name: 'paymentMethodLine',
                                    titleAttribute: 'name',
                                    modifyQueryUsing: function ($query) {
                                        return $query
                                            ->whereHas('paymentMethod', fn ($q) => $q->where('payment_type', 'inbound'))
                                            ->whereHas('journal', fn ($q) => $q->where('type', 'bank'));
                                    }
                                )
                                ->required()
                                ->label('Payment Method')
                                ->searchable()
                                ->preload(),
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
                                    return $record?->partner?->bankAccounts?->first()?->id;
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
                $this->registerPayment($record, $data);

                $payment = $this->createPayment($record, $data);

                $newMove = $this->createMove($record, $payment, $data);

                $this->createMoveLine($record, $newMove, $payment, $data);

                if (
                    $record->reversedEntry
                    && $record->reversedEntry->payment_state == PaymentState::NOT_PAID
                ) {
                    $record->reversedEntry->update(['payment_state' => PaymentState::REVERSED]);
                }

                $record->update(['payment_state' => PaymentState::PAID]);
            })
            ->hidden(function (Move $record) {
                return
                    $record->state != MoveState::POSTED
                    || ! in_array($record->payment_state, [PaymentState::NOT_PAID, PaymentState::PARTIAL, PaymentState::IN_PAYMENT]);
            });
    }

    private function registerPayment(Move $record, array $data): PaymentRegister
    {
        $paymentMethodLine = $record->paymentMethodLine()->findOrFail($data['payment_method_line_id']);

        $paymentRegister = PaymentRegister::Create([
            'move_id'                => $record->id,
            'amount'                 => $data['amount'],
            'payment_method_line_id' => $data['payment_method_line_id'],
            'payment_date'           => $data['payment_date'],
            'partner_bank_id'        => $data['partner_bank_id'],
            'communication'          => $data['communication'],
            'creator_id'             => Auth::id(),
            'source_currency_id'     => $record->currency_id,
            'company_id'             => $record->company_id,
            'partner_id'             => $record->partner_id,
            'payment_type'           => $paymentMethodLine?->paymentMethod?->payment_type,
            'payment_date'           => now(),
            'source_amount'          => $data['amount'],
            'source_amount_currency' => $data['amount'],
        ]);

        $paymentRegister->registerMoveLines()->sync($record->paymentTermLine->id);

        return $paymentRegister;
    }

    private function createPayment(Move $record, array $data): Payment
    {
        $paymentMethodLine = $record->paymentMethodLine()->findOrFail($data['payment_method_line_id']);

        $payment = Payment::create([
            'move_id'                        => $record->id,
            'amount'                         => $data['amount'],
            'payment_method_line_id'         => $data['payment_method_line_id'],
            'payment_method_id'              => $paymentMethodLine->payment_method_id,
            'currency_id'                    => $record->currency_id,
            'partner_bank_id'                => $data['partner_bank_id'],
            'communication'                  => $data['communication'],
            'creator_id'                     => Auth::id(),
            'source_currency_id'             => $record->currency_id,
            'company_id'                     => $record->company_id,
            'partner_id'                     => $record->partner_id,
            'payment_type'                   => $record->paymentMethodLine()->findOrFail($data['payment_method_line_id'])->paymentMethod->payment_type,
            'source_amount'                  => $data['amount'],
            'source_amount_currency'         => $data['amount'],
            'name'                           => str_replace('INV', 'P'.$paymentMethodLine?->journal?->code, $record->name),
            'state'                          => PaymentState::PAID,
            'payment_type'                   => $paymentMethodLine?->paymentMethod?->payment_type,
            'partner_type'                   => $record->partner->sub_type,
            'memo'                           => $data['communication'],
            'amount_company_currency_signed' => $data['amount'],
            'date'                           => now(),
        ]);

        $payment->accountMovePayment()->sync($record->id);

        return $payment;
    }

    private function createMove(Move $record, Payment $payment, array $data)
    {
        $move = $record->replicate();

        $paymentMethodLine = $record->paymentMethodLine()->findOrFail($data['payment_method_line_id']);

        $move->fill([
            'state'                             => MoveState::POSTED,
            'date'                              => now(),
            'origin_payment_id'                 => $payment->id,
            'partner_shipping_id'               => null,
            'invoice_user_id'                   => null,
            'sequence_prefix'                   => str_replace('INV', 'P'.$paymentMethodLine?->journal?->code, $record->name),
            'name'                              => str_replace('INV', 'P'.$paymentMethodLine?->journal?->code, $record->name),
            'reference'                         => $record->reference,
            'move_type'                         => MoveType::ENTRY,
            'state'                             => MoveState::POSTED,
            'payment_date'                      => now(),
            'amount_untaxed'                    => 0.00,
            'amount_tax'                        => 0.00,
            'amount_total'                      => $record->amount_total,
            'amount_residual'                   => 0.00,
            'amount_untaxed_signed'             => 0.00,
            'amount_untaxed_in_currency_signed' => 0.00,
            'amount_tax_signed'                 => 0.00,
            'amount_total_signed'               => $record->amount_total_signed,
            'amount_total_in_currency_signed'   => $record->amount_total_in_currency_signed,
            'amount_residual_signed'            => 0.00,
            'payment_state'                     => PaymentState::NOT_PAID,
            'company_id'                        => $record->company_id,
            'partner_id'                        => $record->partner_id,
            'partner_bank_id'                   => $data['partner_bank_id'],
            'creator_id'                        => Auth::id(),
            'date'                              => now(),
        ]);

        $move->save();

        return $move;
    }

    private function createMoveLine(Move $record, Move $newMove, Payment $payment, array $data)
    {
        MoveLine::create([
            'move_id'                  => $newMove->id,
            'company_id'               => $newMove->company_id,
            'company_currency_id'      => $newMove->company_currency_id,
            'currency_id'              => $newMove->currency_id,
            'partner_id'               => $newMove->partner_id,
            'payment_id'               => $payment->id,
            'move_name'                => $newMove->name,
            'parent_state'             => $newMove->state,
            'reference'                => $newMove->reference,
            'name'                     => "{$payment->name} - {$newMove->name}",
            'display_type'             => DisplayType::PRODUCT,
            'date'                     => now(),
            'date_maturity'            => $record->paymentTermLine->date_maturity,
            'debit'                    => $data['amount'],
            'credit'                   => 0.00,
            'balance'                  => $data['amount'],
            'amount_currency'          => $data['amount'],
            'amount_residual'          => $data['amount'],
            'amount_residual_currency' => $data['amount'],
            'quantity'                 => 1,
            'price_unit'               => 0.00,
            'price_subtotal'           => $data['amount'],
            'price_total'              => $data['amount'],
        ]);

        MoveLine::create([
            'move_id'                  => $newMove->id,
            'company_id'               => $newMove->company_id,
            'company_currency_id'      => $newMove->company_currency_id,
            'currency_id'              => $newMove->currency_id,
            'partner_id'               => $newMove->partner_id,
            'payment_id'               => $payment->id,
            'move_name'                => $newMove->name,
            'parent_state'             => $newMove->state,
            'reference'                => $newMove->reference,
            'name'                     => "{$payment->name} - {$newMove->name}",
            'display_type'             => DisplayType::PRODUCT,
            'date'                     => now(),
            'date_maturity'            => $record->paymentTermLine->date_maturity,
            'debit'                    => 0.00,
            'credit'                   => $data['amount'],
            'balance'                  => -$data['amount'],
            'amount_currency'          => -$data['amount'],
            'amount_residual'          => 0.00,
            'amount_residual_currency' => 0.00,
            'quantity'                 => 1,
            'price_unit'               => 0.00,
            'price_subtotal'           => -$data['amount'],
            'price_total'              => -$data['amount'],
        ]);
    }
}
