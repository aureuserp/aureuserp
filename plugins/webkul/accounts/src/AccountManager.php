<?php

namespace Webkul\Account;

use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Webkul\Account\Facades\Tax;
use Webkul\Account\Models\Move as AccountMove;
use Webkul\Account\Models\MoveLine;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\PaymentState;
use Webkul\Account\Mail\Invoice\Actions\InvoiceEmail;
use Webkul\Account\Models\Partner;
use Webkul\Support\Services\EmailService;

class AccountManager
{
    public function cancel(AccountMove $record): AccountMove
    {
        $record->state = MoveState::CANCEL;

        $record->save();

        $record = $this->computeAccountMove($record);

        return $record;
    }

    public function confirm(AccountMove $record): AccountMove
    {
        $record->state = MoveState::POSTED;

        $record->save();

        $record = $this->computeAccountMove($record);

        return $record;
    }

    public function setAsChecked(AccountMove $record): AccountMove
    {
        $record->checked = true;

        $record->save();

        $record = $this->computeAccountMove($record);

        return $record;
    }

    public function resetToDraft(AccountMove $record): AccountMove
    {
        $record->state = MoveState::DRAFT;

        $record->payment_state = PaymentState::NOT_PAID;

        $record->save();

        $record = $this->computeAccountMove($record);

        return $record;
    }

    public function printAndSend(AccountMove $record, array $data): AccountMove
    {
        $partners = Partner::whereIn('id', $data['partners'])->get();

        $viewTemplate = 'accounts::mail/invoice/actions/invoice';

        foreach ($partners as $partner) {
            if (! $partner->email) {
                continue;
            }

            $attachments = [];

            foreach ($data['files'] as $file) {
                $attachments[] = [
                    'path' => asset('storage/' . $file),
                    'name' => basename($file),
                ];
            }

            app(EmailService::class)->send(
                mailClass: InvoiceEmail::class,
                view: $viewTemplate,
                payload: $this->preparePayloadForSendByEmail($record, $partner, $data),
                attachments: $attachments,
            );
        }

        $messageData = [
            'from' => [
                'company' => Auth::user()->defaultCompany->toArray(),
            ],
            'body' => view($viewTemplate, [
                'payload' => $this->preparePayloadForSendByEmail($record, $partner, $data),
            ])->render(),
            'type' => 'comment',
        ];

        $record->addMessage($messageData, Auth::user()->id);

        Notification::make()
            ->success()
            ->title(__('accounts::filament/resources/invoice/actions/print-and-send.modal.notification.invoice-sent.title'))
            ->body(__('accounts::filament/resources/invoice/actions/print-and-send.modal.notification.invoice-sent.body'))
            ->send();

        return $record;
    }

    public function computeAccountMove(AccountMove $record): AccountMove
    {
        $record->amount_untaxed = 0;
        $record->amount_tax = 0;
        $record->amount_total = 0;
        $record->amount_residual = 0;
        $record->amount_untaxed_signed = 0;
        $record->amount_untaxed_in_currency_signed = 0;
        $record->amount_tax_signed = 0;
        $record->amount_total_signed = 0;
        $record->amount_total_in_currency_signed = 0;
        $record->amount_residual_signed = 0;

        $newTaxEntries = [];
        $signMultiplier = $this->getSignMultiplier($record);

        foreach ($record->lines as $line) {
            $line->parent_state = $record->state;

            [$line, $amountTax] = $this->collectLineTotals($line, $newTaxEntries);

            $record->amount_untaxed += floatval($line->price_subtotal);
            $record->amount_tax += floatval($amountTax);
            $record->amount_total += floatval($line->price_total);

            $record->amount_untaxed_signed += floatval($line->price_subtotal) * $signMultiplier;
            $record->amount_untaxed_in_currency_signed += floatval($line->price_subtotal) * $signMultiplier;
            $record->amount_tax_signed += floatval($amountTax) * $signMultiplier;
            $record->amount_total_signed += floatval($line->price_total) * $signMultiplier;
            $record->amount_total_in_currency_signed += floatval($line->price_total) * $signMultiplier;

            $record->amount_residual += floatval($line->price_total);
            $record->amount_residual_signed += floatval($line->price_total) * $signMultiplier;
        }

        $record->save();

        $this->updateTaxLines($record, $newTaxEntries);

        $this->updateOrCreatePaymentTermLine($record);

        return $record;
    }

    /**
     * Get sign multiplier based on document type
     */
    private function getSignMultiplier(AccountMove $record): int
    {
        if (in_array($record->move_type, ['out_refund', 'in_refund'])) {
            return -1;
        }

        return 1;
    }

    /**
     * Collect line totals and tax information
     */
    public function collectLineTotals(MoveLine $line, array &$newTaxEntries): array
    {
        $subTotal = $line->price_unit * $line->quantity;

        if ($line->discount > 0) {
            $discountAmount = $subTotal * ($line->discount / 100);

            $subTotal = $subTotal - $discountAmount;
        }

        $taxIds = $line->taxes->pluck('id')->toArray();

        [$subTotal, $taxAmount, $taxesComputed] = Tax::collect($taxIds, $subTotal, $line->quantity);

        foreach ($taxesComputed as $taxComputed) {
            $taxId = $taxComputed['tax_id'];

            if (! isset($newTaxEntries[$taxId])) {
                $newTaxEntries[$taxId] = [
                    'tax_id' => $taxId,
                    'tax_base_amount' => 0,
                    'tax_amount' => 0,
                ];
            }

            $newTaxEntries[$taxId]['tax_base_amount'] += $subTotal;
            $newTaxEntries[$taxId]['tax_amount'] += $taxComputed['tax_amount'];
        }

        $line->price_subtotal = round($subTotal, 4);
        $line->price_total = $subTotal + $taxAmount;

        $line = $this->computeBalance($line);
        $line = $this->computeCreditAndDebit($line);
        $line = $this->computeAmountCurrency($line);

        $line->save();

        return [
            $line,
            $taxAmount,
        ];
    }

    /**
     * Compute debit and credit based on balance and move type
     */
    private function computeCreditAndDebit(MoveLine $line): MoveLine
    {
        $move = $line->move;
        $isInverseSign = in_array($move->move_type, ['out_refund', 'in_refund']);

        if (! $move->is_storno) {
            if (! $isInverseSign) {
                $line->debit = $line->balance > 0.0 ? $line->balance : 0.0;
                $line->credit = $line->balance < 0.0 ? -$line->balance : 0.0;
            } else {
                $line->debit = $line->balance < 0.0 ? -$line->balance : 0.0;
                $line->credit = $line->balance > 0.0 ? $line->balance : 0.0;
            }
        } else {
            if (! $isInverseSign) {
                $line->debit = $line->balance < 0.0 ? $line->balance : 0.0;
                $line->credit = $line->balance > 0.0 ? -$line->balance : 0.0;
            } else {
                $line->debit = $line->balance > 0.0 ? -$line->balance : 0.0;
                $line->credit = $line->balance < 0.0 ? $line->balance : 0.0;
            }
        }

        return $line;
    }

    /**
     * Compute line balance based on document type
     */
    private function computeBalance(MoveLine $line): MoveLine
    {
        $move = $line->move;

        $isInverseSign = in_array($move->move_type, ['out_refund', 'in_refund']);

        $line->balance = $isInverseSign ? $line->price_subtotal : -$line->price_subtotal;

        return $line;
    }

    /**
     * Compute amount in currency
     */
    private function computeAmountCurrency(MoveLine $line): MoveLine
    {
        if (is_null($line->amount_currency)) {
            $line->amount_currency = round($line->balance * $line->currency_rate, 2);
        }

        if ($line->currency_id === $line->company->currency_id) {
            $line->amount_currency = $line->balance;
        }

        return $line;
    }

    /**
     * Prepare payment term line data
     */
    private function preparePaymentTermLine(AccountMove $move): array
    {
        $dateMaturity = $move->invoice_date_due;

        if (
            $move->invoicePaymentTerm
            && $move->invoicePaymentTerm->dueTerm?->nb_days
        ) {
            $dateMaturity = $dateMaturity->addDays($move->invoicePaymentTerm->dueTerm->nb_days);
        }

        $signMultiplier = $this->getSignMultiplier($move);
        $amount = abs($move->amount_total);

        if ($signMultiplier < 0) {
            $debit = 0;
            $credit = $amount;
            $balance = -$amount;
        } else {
            $debit = $amount;
            $credit = 0;
            $balance = $amount;
        }

        return [
            'move_id'                  => $move->id,
            'move_name'                => $move->name,
            'display_type'             => 'payment_term',
            'currency_id'              => $move->currency_id,
            'partner_id'               => $move->partner_id,
            'date_maturity'            => $dateMaturity,
            'company_id'               => $move->company_id,
            'company_currency_id'      => $move->company_currency_id,
            'commercial_partner_id'    => $move->partner_id,
            'sort'                     => MoveLine::max('sort') + 1,
            'parent_state'             => $move->state,
            'date'                     => now(),
            'creator_id'               => $move->creator_id,
            'debit'                    => $debit,
            'credit'                   => $credit,
            'balance'                  => $balance,
            'amount_currency'          => $balance,
            'amount_residual'          => $amount,
            'amount_residual_currency' => $amount,
        ];
    }

    /**
     * Update or create the payment term line
     */
    private function updateOrCreatePaymentTermLine($move): void
    {
        MoveLine::updateOrCreate(
            [
                'move_id'      => $move->id,
                'display_type' => 'payment_term',
            ],
            $this->preparePaymentTermLine($move)
        );
    }

    /**
     * Update tax lines for the move
     */
    private function updateTaxLines(AccountMove $move, array $newTaxEntries): void
    {
        $existingTaxLines = MoveLine::where('move_id', $move->id)
            ->where('display_type', 'tax')
            ->get()
            ->keyBy('tax_line_id');

        $signMultiplier = $this->getSignMultiplier($move);

        foreach ($newTaxEntries as $taxId => $taxData) {
            $tax = \Webkul\Account\Models\Tax::find($taxId);
            if (! $tax) {
                continue;
            }

            $currentTaxAmount = $taxData['tax_amount'];

            if ($signMultiplier < 0) {
                $debit = $currentTaxAmount;
                $credit = 0;
                $balance = $currentTaxAmount;
                $amountCurrency = $currentTaxAmount;
            } else {
                $debit = 0;
                $credit = $currentTaxAmount;
                $balance = -$currentTaxAmount;
                $amountCurrency = -$currentTaxAmount;
            }

            $taxLineData = [
                'name'                  => $tax->name,
                'move_id'               => $move->id,
                'move_name'             => $move->name,
                'display_type'          => 'tax',
                'currency_id'           => $move->currency_id,
                'partner_id'            => $move->partner_id,
                'company_id'            => $move->company_id,
                'company_currency_id'   => $move->company_currency_id,
                'commercial_partner_id' => $move->partner_id,
                'parent_state'          => $move->state,
                'date'                  => now(),
                'creator_id'            => $move->creator_id,
                'debit'                 => $debit,
                'credit'                => $credit,
                'balance'               => $balance,
                'amount_currency'       => $amountCurrency,
                'tax_base_amount'       => $taxData['tax_base_amount'],
                'tax_line_id'           => $taxId,
                'tax_group_id'          => $tax->tax_group_id,
            ];

            if (isset($existingTaxLines[$taxId])) {
                $existingTaxLines[$taxId]->update($taxLineData);

                unset($existingTaxLines[$taxId]);
            } else {
                $taxLineData['sort'] = MoveLine::max('sort') + 1;

                MoveLine::create($taxLineData);
            }
        }

        $existingTaxLines->each->delete();
    }

    private function preparePayloadForSendByEmail($record, $partner, $data)
    {
        return [
            'record_name'    => $record->name,
            'model_name'     => class_basename($record),
            'subject'        => $data['subject'],
            'description'    => $data['description'],
            'to'             => [
                'address' => $partner?->email,
                'name'    => $partner?->name,
            ],
        ];
    }
}
