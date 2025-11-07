<?php

namespace Webkul\Account;

use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Webkul\Account\Enums\DelayType;
use Webkul\Account\Enums\DisplayType;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\PaymentState;
use Webkul\Account\Facades\Tax as TaxFacade;
use Webkul\Account\Mail\Invoice\Actions\InvoiceEmail;
use Webkul\Account\Models\Move;
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

    public function computeAccountMove(AccountMove $record): AccountMove
    {
        $this->syncDynamicLines($record);

        $record->refresh();

        foreach ($record->invoiceLines as $line) {
            $line = $this->computeMoveLineTotals($line);
            
            $line->save();
        }

        $record = $this->computeMoveTotals($record);

        $record->save();

        return $record;
    }

    public function computeMoveTotals(Move $move): Move
    {
        $totalUntaxed = $totalUntaxedCurrency = 0.0;

        $totalTax = $totalTaxCurrency = 0.0;

        $totalResidual = $totalResidualCurrency = 0.0;

        $total = $totalCurrency = 0.0;

        foreach ($move->lines as $line) {
            if ($move->isInvoice(true)) {
                if (
                    $line->display_type == DisplayType::TAX
                    || (
                        $line->display_type == DisplayType::ROUNDING
                        && $line->tax_repartition_line_id
                    )
                ) {
                    $totalTax += $line->balance;

                    $totalTaxCurrency += $line->amount_currency;

                    $total += $line->balance;

                    $totalCurrency += $line->amount_currency;
                } elseif (in_array($line->display_type, [
                    DisplayType::PRODUCT,
                    DisplayType::ROUNDING
                ])) {
                    $totalUntaxed += $line->balance;

                    $totalUntaxedCurrency += $line->amount_currency;

                    $total += $line->balance;

                    $totalCurrency += $line->amount_currency;
                }
            } elseif ($line->display_type == DisplayType::PAYMENT_TERM) {
                $totalResidual += $line->amount_residual;

                $totalResidualCurrency += $line->amount_residual_currency;
            } else {
                if ($line->debit) {
                    $total += $line->balance;

                    $totalCurrency += $line->amount_currency;
                }
            }
        }

        $sign = $move->direction_sign;

        $move->amount_untaxed = $sign * $totalUntaxedCurrency;

        $move->amount_tax = $sign * $totalTaxCurrency;

        $move->amount_total = $sign * $totalCurrency;

        $move->amount_residual = -$sign * $totalResidualCurrency;

        $move->amount_untaxed_signed = -$totalUntaxed;

        $move->amount_untaxed_in_currency_signed = -$totalUntaxedCurrency;

        $move->amount_tax_signed = -$totalTax;

        if ($move->move_type == MoveType::ENTRY) {
            $move->amount_total_signed = abs($total);
        } else {
            $move->amount_total_signed = -$total;
        }

        $move->amount_residual_signed = $totalResidual;
        
        if ($move->move_type == MoveType::ENTRY) {
            $move->amount_total_in_currency_signed = abs($move->amount_total);
        } else {
            $move->amount_total_in_currency_signed = -($sign * $move->amount_total);
        }

        return $move;
    }

    public function computeMoveLineTotals(MoveLine $line): MoveLine
    {
        if (! in_array($line->display_type, [DisplayType::PRODUCT, DisplayType::COGS])) {
            $line->price_total = 0.0;

            $line->price_subtotal = 0.0;

            return $line;
        }

        $baseLine = $this->prepareProductBaseLineForTaxesComputation($line);

        $baseLine = TaxFacade::addTaxDetailsInBaseLine($baseLine, $line->company);

        $line->price_subtotal = $baseLine['tax_details']['raw_total_excluded_currency'];

        $line->price_total = $baseLine['tax_details']['raw_total_included_currency'];

        $line->computeBalance();

        $line->computeCreditAndDebit();

        $line->computeAmountCurrency();

        return $line;
    }

    public function syncDynamicLines(Move $move)
    {
        $this->syncTaxLines($move);

        $this->syncPaymentTermLines($move);
    }

    public function syncTaxLines(Move $move)
    {
        if (! $move->isInvoice(true)) {
            return;
        }

        $taxLines = $move->lines->whereNotNull('tax_repartition_line_id');

        $roundFromTaxLines = ! $move->isInvoice(true) && $taxLines->isNotEmpty();

        [$baseLinesValues, $taxLinesValues] = $this->getRoundedBaseAndTaxLines($move, $roundFromTaxLines);
        
        $baseLinesValues = TaxFacade::addAccountingDataInBaseLinesTaxDetails(
            $baseLinesValues, 
            $move->company, 
            $move->always_tax_exigible
        );
        
        $taxResults = TaxFacade::prepareTaxLines(
            $baseLinesValues, 
            $move->company, 
            $taxLinesValues
        );

        foreach ($taxResults['base_lines_to_update'] as $baseLine) {
            $baseLine['record']->update([
                'amount_currency' => $baseLine['amount_currency'],
                'balance' => $baseLine['balance'],
            ]);
        }

        foreach ($taxResults['tax_lines_to_delete'] as $taxLineVals) {
            $taxLineVals['record']->delete();
        }

        foreach ($taxResults['tax_lines_to_add'] as $taxLineVals) {
            unset($taxLineVals['tax_ids']);
            
            $taxMoveLine = MoveLine::create(array_merge($taxLineVals, [
                'display_type' => DisplayType::TAX,
                'move_id' => $move->id,
            ]));

            $taxMoveLine->computeCreditAndDebit();

            $taxMoveLine->save();
        }

        foreach ($taxResults['tax_lines_to_update'] as $taxLineVals) {
            unset($taxLineVals['tax_ids']);

            $taxMoveLine = $taxLineVals['record']->update($taxLineVals);

            $taxMoveLine->computeCreditAndDebit();

            $taxMoveLine->save();
        }
    }

    public function syncPaymentTermLines(Move $move)
    {
        if (! $move->isInvoice(true)) {
            return;
        }

        $neededTerms = $this->prepareNeededTerms($move);

        $existingLines = $move->lines
            ->where('display_type', DisplayType::PAYMENT_TERM)
            ->keyBy(fn ($line) => json_encode($line->term_key ?? []));

        $neededMapping = collect($neededTerms)->mapWithKeys(function ($data) {
            $key = [
                'move_id' => $data['move_id'],
                'date_maturity' => $data['date_maturity'],
                'discount_date' => $data['discount_date'],
            ];

            return [json_encode($key) => [
                'key' => $key,
                'values' => [
                    'balance' => $data['balance'],
                    'amount_currency' => $data['amount_currency'],
                    'discount_date' => $data['discount_date'],
                    'discount_balance' => $data['discount_balance'],
                    'discount_amount_currency' => $data['discount_amount_currency'],
                ],
            ]];
        });

        foreach ($existingLines as $keyStr => $line) {
            if (! $neededMapping->has($keyStr)) {
                $line->delete();

                $existingLines->forget($keyStr);
            }
        }

        foreach ($neededMapping as $keyStr => $needed) {
            $attributes = array_merge($needed['values'], $needed['key']);

            if ($existingLines->has($keyStr)) {
                $existingLines[$keyStr]->update($attributes);

                $existingLines[$keyStr]->computeCreditAndDebit();

                $existingLines[$keyStr]->save();
            } else {
                $moveLine = MoveLine::create(array_merge($attributes, [
                    'display_type' => DisplayType::PAYMENT_TERM,
                ]));

                $moveLine->computeCreditAndDebit();

                $moveLine->save();
            }
        }
    }


    protected function getRoundedBaseAndTaxLines(Move $move, $roundFromTaxLines = true)
    {
        $baseAmls = $move->lines->where('display_type', DisplayType::PRODUCT);
        
        $baseLines = [];

        foreach ($baseAmls as $line) {
            $baseLines[] = $this->prepareProductBaseLineForTaxesComputation($line);
        }

        $taxLines = [];
        
        $cashRoundingAmls = $move->lines
            ->where('display_type', DisplayType::ROUNDING)
            ->whereNull('tax_repartition_line_id');

        foreach ($cashRoundingAmls as $line) {
            $baseLines[] = $move->prepareCashRoundingBaseLineForTaxesComputation($move, $line);
        }
        
        $baseLines = TaxFacade::addTaxDetailsInBaseLines($baseLines, $move->company);
        
        $taxAmls = $move->lines->whereNotNull('tax_repartition_line_id');

        foreach ($taxAmls as $taxLine) {
            $taxLines[] = TaxFacade::prepareTaxLineForTaxesComputation($taxLine);
        }
        
        $baseLines = TaxFacade::roundBaseLinesTaxDetails(
            $baseLines, 
            $move->company, 
            $roundFromTaxLines ? $taxLines : []
        );

        return [$baseLines, $taxLines];
    }

    public function prepareProductBaseLineForTaxesComputation(MoveLine $line)
    {
        $isInvoice = $line->move->isInvoice(true);

        $sign = $isInvoice ? $line->move->direction_sign : 1;

        if ($isInvoice) {
            $rate = $line->move->invoice_currency_rate;
        } else {
            $rate = $line->balance ? abs($line->amount_currency) / abs($line->balance) : 0.0;
        }

        return TaxFacade::prepareBaseLineForTaxesComputation(
            $line,
            priceUnit: $isInvoice ? $line->price_unit : $line->amount_currency,
            quantity: $isInvoice ? $line->quantity : 1.0,
            discount: $isInvoice ? $line->discount : 0.0,
            rate: $rate,
            sign: $sign,
            specialMode: $isInvoice ? false : 'total_excluded',
        );
    }

    public function prepareCashRoundingBaseLineForTaxesComputation(Move $move, MoveLine $line)
    {
        $sign = $move->direction_sign;

        $rate = $move->invoice_currency_rate;

        return TaxFacade::prepareBaseLineForTaxesComputation(
            $line,
            priceUnit: $sign * $line->amount_currency,
            quantity: 1.0,
            sign: $sign,
            specialMode: 'total_excluded',
            special_type: 'cash_rounding',
            is_refund: in_array($move->move_type, [MoveType::OUT_REFUND, MoveType::IN_REFUND]),
            rate: $rate,
        );
    }

    public function prepareNeededTerms(AccountMove $move)
    {
        $neededTerms = [];
        
        $sign = $move->isInbound(true) ? 1 : -1;
        
        if ($move->isInvoice(true) && $move->invoiceLines()->exists()) {
            $taxAmountCurrency = 0.0;
            
            $taxAmount = $taxAmountCurrency;

            $untaxedAmountCurrency = 0.0;

            $untaxedAmount = $untaxedAmountCurrency;

            $sign = $move->direction_sign;
            
            [$baseLines, $taxLines] = $this->getRoundedBaseAndTaxLines($move, false);

            $baseLines = TaxFacade::addAccountingDataInBaseLinesTaxDetails($baseLines, $move->company, $move->always_tax_exigible);

            $taxResults = TaxFacade::prepareTaxLines($baseLines, $move->company);

            foreach ($taxResults['base_lines_to_update'] as $baseLine) {
                $untaxedAmountCurrency += $sign * $baseLine['amount_currency'];

                $untaxedAmount += $sign * $baseLine['balance'];
            }
            
            foreach ($taxResults['tax_lines_to_add'] as $taxLineVals) {
                $taxAmountCurrency += $sign * $taxLineVals['amount_currency'];

                $taxAmount += $sign * $taxLineVals['balance'];
            }

            if ($move->invoice_payment_term_id) { 
                $invoicePaymentTerms = $move->invoicePaymentTerm->computeTerms(
                    dateRef: $move->invoice_date ?? $move->date ?? now(),
                    currency: $move->currency,
                    taxAmountCurrency: $taxAmountCurrency,
                    taxAmount: $taxAmount,
                    untaxedAmountCurrency: $untaxedAmountCurrency,
                    untaxedAmount: $untaxedAmount,
                    company: $move->company,
                    cashRounding: $move->invoiceCashRounding,
                    sign: $sign
                );

                foreach ($invoicePaymentTerms['lines'] as $termLine) {
                    $key = [
                        'move_id' => $move->id,
                        'date_maturity' => $termLine['date']?->toDateString(),
                        'discount_date' => $invoicePaymentTerms['discount_date']?->toDateString(),
                    ];

                    $values = [
                        'balance' => $termLine['company_amount'],
                        'amount_currency' => $termLine['foreign_amount'],
                        'discount_date' => $invoicePaymentTerms['discount_date'] ?? null,
                        'discount_balance' => $invoicePaymentTerms['discount_balance'] ?? 0.0,
                        'discount_amount_currency' => $invoicePaymentTerms['discount_amount_currency'] ?? 0.0,
                    ];
                    
                    $keyStr = json_encode($key);

                    if (!isset($neededTerms[$keyStr])) {
                        $neededTerms[$keyStr] = array_merge($key, $values);
                    } else {
                        $neededTerms[$keyStr]['balance'] += $values['balance'];

                        $neededTerms[$keyStr]['amount_currency'] += $values['amount_currency'];
                    }
                }
            } else {
                $key = [
                    'move_id' => $move->id,
                    'date_maturity' => $move->invoice_date_due?->toDateString(),
                    'discount_date' => false,
                    'discount_balance' => 0.0,
                    'discount_amount_currency' => 0.0
                ];
                
                $keyStr = json_encode($key);

                $neededTerms[$keyStr] = array_merge($key, [
                    'balance' => $untaxedAmount + $taxAmount,
                    'amount_currency' => $untaxedAmountCurrency + $taxAmountCurrency,
                ]);
            }
        }
        
        return $neededTerms;
    }
}
