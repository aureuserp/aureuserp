<?php

namespace Webkul\Account;

use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Webkul\Account\Enums\DelayType;
use Webkul\Account\Enums\DisplayType;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\PaymentState;
use Webkul\Account\Facades\Tax as TaxFacade;
use Webkul\Account\Mail\Invoice\Actions\InvoiceEmail;
use Webkul\Account\Models\Account;
use Webkul\Account\Models\FiscalPosition;
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
                    'path' => $file,
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
        foreach ($record->invoiceLines as $line) {
            $line->move->syncDynamicLines();

            $line->computeTotals();
            
            $line->save();
        }

        $record->computeTotals();

        $record->save();

        return $record;
    }

    private function syncDynamicLines(AccountMove $move, array $newTaxEntries): void
    {
        $this->syncTaxLines($move, $newTaxEntries);

        $this->syncDynamicLine($move);
    }

    /**
     * Update tax lines for the move
     */
    private function syncTaxLines(AccountMove $move, array $newTaxEntries): void
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
                'display_type'             => DisplayType::TAX,
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
    private function syncDynamicLine(AccountMove $move, $moveType = 'payment_term'): void
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
            'display_type' => DisplayType::PAYMENT_TERM,
        ], [
            'move_id'                  => $move->id,
            'move_name'                => $move->name,
            'display_type'             => DisplayType::PAYMENT_TERM,
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
        if ($record->isEntry() || $record->isOutbound()) {
            return -1;
        }

        return 1;
    }
}
