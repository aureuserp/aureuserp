<?php

namespace Webkul\Account;

use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\PaymentState;
use Webkul\Account\Facades\Tax as TaxFacade;
use Webkul\Account\Mail\Invoice\Actions\InvoiceEmail;
use Webkul\Account\Models\Journal;
use Webkul\Account\Models\Move as AccountMove;
use Webkul\Account\Models\MoveLine;
use Webkul\Account\Models\Partner;
use Webkul\Account\Models\Tax;
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
                    'path' => asset('storage/'.$file),
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

        $record = $this->computePartnerDisplayInfo($record);

        $record = $this->computeInvoiceCurrencyRate($record);

        $record = $this->computeJournalId($record);

        $record = $this->computePartnerShippingId($record);

        $record = $this->computeCommercialPartnerId($record);

        $record = $this->computeInvoiceDateDue($record);

        $signMultiplier = $this->getSignMultiplier($record);

        foreach ($record->lines as $line) {
            $line = $this->computeMoveLine($record, $line);

            [$line, $amountTax] = $this->computeMoveLineTotals($line, $newTaxEntries);

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

        $this->computeTaxLines($record, $newTaxEntries);

        $this->computePaymentTermLine($record);

        return $record;
    }

    public function computePartnerDisplayInfo(AccountMove $record): AccountMove
    {
        $vendorDisplayName = $record->partner?->name;

        if (! $vendorDisplayName) {
            if ($record->invoice_source_email) {
                $vendorDisplayName = "@From: {$record->invoice_source_email}";
            } else {
                $vendorDisplayName = "#Created by: {$record->createdBy->name}";
            }
        }

        $record->invoice_partner_display_name = $vendorDisplayName;

        return $record;
    }

    public function computeInvoiceCurrencyRate(AccountMove $record): AccountMove
    {
        $record->invoice_currency_rate = 1;

        return $record;
    }

    public function computeJournalId(AccountMove $record): AccountMove
    {
        if (! in_array($record->journal?->type, $record->getValidJournalTypes())) {
            $record->journal_id = $this->searchDefaultJournal($record)?->id;
        }

        return $record;
    }

    public function searchDefaultJournal(AccountMove $record): ?Journal
    {
        $validJournalTypes = $record->getValidJournalTypes();

        return Journal::where('company_id', $record->company_id)
            ->whereIn('type', $validJournalTypes)
            ->first();
    }

    public function computeCommercialPartnerId(AccountMove $record): AccountMove
    {
        $record->commercial_partner_id = $record->partner_id;

        return $record;
    }

    public function computePartnerShippingId(AccountMove $record): AccountMove
    {
        $record->partner_shipping_id = $record->partner_id;

        return $record;
    }

    public static function computeInvoiceDateDue(AccountMove $move): AccountMove
    {
        $dateMaturity = now();

        if ($move->invoicePaymentTerm) {
            $dueTerm = $move->invoicePaymentTerm->dueTerm;

            if ($dueTerm) {
                switch ($dueTerm->delay_type) {
                    case Enums\DelayType::DAYS_AFTER->value:
                        $dateMaturity = $dateMaturity->addDays($dueTerm->nb_days);

                        break;

                    case Enums\DelayType::DAYS_AFTER_END_OF_MONTH->value:
                        $dateMaturity = $dateMaturity->endOfMonth()->addDays($dueTerm->nb_days);
                        break;

                    case Enums\DelayType::DAYS_AFTER_END_OF_NEXT_MONTH->value:
                        $dateMaturity = $dateMaturity->addMonth()->endOfMonth()->addDays($dueTerm->days_next_month);

                        break;

                    case Enums\DelayType::DAYS_END_OF_MONTH_NO_THE->value:
                        $dateMaturity = $dateMaturity->endOfMonth();

                        break;
                }
            }
        }

        $move->invoice_date_due = $dateMaturity;

        return $move;
    }

    /**
     * Collect line totals and tax information
     */
    public function computeMoveLine(AccountMove $move, MoveLine $line): MoveLine
    {
        $line->move_name = $move->name;

        $line->name = $line->product->name;

        $line->parent_state = $move->state;

        $line->date_maturity = $move->invoice_date_due;

        $line->discount_date = $line->discount > 0 ? now() : null;

        $line->uom_id = $line->uom_id ?? $line->product->uom_id;

        $line->partner_id = $move->partner_id;

        $line->journal_id = $move->journal_id;

        $line->currency_id = $move->currency_id;

        // Todo:: check this
        $line->company_currency_id = $move->currency_id;

        $line->company_id = $move->company_id;

        return $line;
    }

    /**
     * Collect line totals and tax information
     */
    public function computeMoveLineTotals(MoveLine $line, array &$newTaxEntries): array
    {
        $subTotal = $line->price_unit * $line->quantity;

        if ($line->discount > 0) {
            $discountAmount = $subTotal * ($line->discount / 100);

            $subTotal = $subTotal - $discountAmount;
        }

        $taxIds = $line->taxes->pluck('id')->toArray();

        [$subTotal, $taxAmount, $taxesComputed] = TaxFacade::collect($taxIds, $subTotal, $line->quantity);

        foreach ($taxesComputed as $taxComputed) {
            $taxId = $taxComputed['tax_id'];

            if (! isset($newTaxEntries[$taxId])) {
                $newTaxEntries[$taxId] = [
                    'tax_id'          => $taxId,
                    'tax_base_amount' => 0,
                    'tax_amount'      => 0,
                ];
            }

            $newTaxEntries[$taxId]['tax_base_amount'] += $subTotal;

            $newTaxEntries[$taxId]['tax_amount'] += $taxComputed['tax_amount'];
        }

        $line->price_subtotal = round($subTotal, 4);
        $line->price_total = $subTotal + $taxAmount;

        $line = $this->computeMoveLineBalance($line);

        $line = $this->computeMoveLineCreditAndDebit($line);

        $line = $this->computeMoveLineAmountCurrency($line);

        $line->save();

        return [
            $line,
            $taxAmount,
        ];
    }

    /**
     * Compute line balance based on document type
     */
    private function computeMoveLineBalance(MoveLine $line): MoveLine
    {
        $line->balance = $line->move->isInbound()
            ? -$line->price_subtotal
            : $line->price_subtotal;

        return $line;
    }

    /**
     * Compute debit and credit based on balance and move type
     */
    private function computeMoveLineCreditAndDebit(MoveLine $line): MoveLine
    {
        if (! $line->move->is_storno) {
            $line->debit = $line->balance > 0.0 ? $line->balance : 0.0;
            $line->credit = $line->balance < 0.0 ? -$line->balance : 0.0;
        } else {
            $line->debit = $line->balance < 0.0 ? $line->balance : 0.0;
            $line->credit = $line->balance > 0.0 ? -$line->balance : 0.0;
        }

        return $line;
    }

    /**
     * Compute amount in currency
     */
    private function computeMoveLineAmountCurrency(MoveLine $line): MoveLine
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
     * Update tax lines for the move
     */
    private function computeTaxLines(AccountMove $move, array $newTaxEntries): void
    {
        $existingTaxLines = MoveLine::where('move_id', $move->id)
            ->where('display_type', 'tax')
            ->get()
            ->keyBy('tax_line_id');

        foreach ($newTaxEntries as $taxId => $taxData) {
            $tax = Tax::find($taxId);

            if (! $tax) {
                continue;
            }

            $currentTaxAmount = $taxData['tax_amount'];

            if ($move->isOutbound()) {
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
                'name'                     => $tax->name,
                'move_id'                  => $move->id,
                'move_name'                => $move->name,
                'display_type'             => Enums\DisplayType::TAX,
                'currency_id'              => $move->currency_id,
                'partner_id'               => $move->partner_id,
                'company_id'               => $move->company_id,
                'company_currency_id'      => $move->company_currency_id,
                'commercial_partner_id'    => $move->partner_id,
                'journal_id'               => $move->journal_id,
                'parent_state'             => $move->state,
                'date'                     => now(),
                'creator_id'               => $move->creator_id,
                'debit'                    => $debit,
                'credit'                   => $credit,
                'balance'                  => $balance,
                'amount_currency'          => $amountCurrency,
                'tax_base_amount'          => $taxData['tax_base_amount'],
                'tax_line_id'              => $taxId,
                'tax_group_id'             => $tax->tax_group_id,
            ];

            if (isset($existingTaxLines[$taxId])) {
                $existingTaxLines[$taxId]->update($taxLineData);

                unset($existingTaxLines[$taxId]);
            } else {
                MoveLine::create($taxLineData);
            }
        }

        $existingTaxLines->each->delete();
    }

    /**
     * Update or create the payment term line
     */
    private function computePaymentTermLine($move): void
    {
        $amount = abs($move->amount_total);

        if ($move->isOutbound()) {
            $debit = 0;
            $credit = $amount;
            $balance = -$amount;
        } else {
            $debit = $amount;
            $credit = 0;
            $balance = $amount;
        }

        MoveLine::updateOrCreate([
            'move_id'      => $move->id,
            'display_type' => Enums\DisplayType::PAYMENT_TERM,
        ], [
            'move_id'                  => $move->id,
            'move_name'                => $move->name,
            'display_type'             => Enums\DisplayType::PAYMENT_TERM,
            'currency_id'              => $move->currency_id,
            'partner_id'               => $move->partner_id,
            'date_maturity'            => $move->invoice_date_due,
            'company_id'               => $move->company_id,
            'company_currency_id'      => $move->company_currency_id,
            'commercial_partner_id'    => $move->partner_id,
            'journal_id'               => $move->journal_id,
            'parent_state'             => $move->state,
            'date'                     => now(),
            'creator_id'               => $move->creator_id,
            'debit'                    => $debit,
            'credit'                   => $credit,
            'balance'                  => $balance,
            'amount_currency'          => $balance,
            'amount_residual'          => $balance,
            'amount_residual_currency' => $balance,
        ]);
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

    /**
     * Get sign multiplier based on document type
     */
    private function getSignMultiplier(AccountMove $record): int
    {
        if ($record->isOutbound()) {
            return -1;
        }

        return 1;
    }
}
