<?php

namespace Webkul\Account;

use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Webkul\Account\Enums\AccountType;
use Webkul\Account\Enums\DisplayType;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Enums\PaymentState;
use Webkul\Account\Facades\Account;
use Webkul\Account\Facades\Tax as TaxFacade;
use Webkul\Account\Mail\Invoice\Actions\InvoiceEmail;
use Webkul\Account\Models\Move;
use Webkul\Account\Models\Move as AccountMove;
use Webkul\Account\Models\MoveLine;
use Webkul\Account\Models\Partner;
use Webkul\Account\Models\Payment;
use Webkul\Support\Models\Currency;
use Webkul\Account\Models\PaymentRegister;
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

        $record->posted_before = true;

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
                    DisplayType::ROUNDING,
                ])) {
                    $totalUntaxed += $line->balance;

                    $totalUntaxedCurrency += $line->amount_currency;

                    $total += $line->balance;

                    $totalCurrency += $line->amount_currency;
                } elseif ($line->display_type == DisplayType::PAYMENT_TERM) {
                    $totalResidual += $line->amount_residual;

                    $totalResidualCurrency += $line->amount_residual_currency;
                }
            } elseif ($line->display_type == DisplayType::PAYMENT_TERM) {
                $totalResidual += $line->amount_residual;

                $totalResidualCurrency += $line->amount_residual_currency;
            } else {
                if ((float) $line->debit) {
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

        $line->computeAmountResidual();

        return $line;
    }

    public function syncDynamicLines(Move $move)
    {
        $this->syncTaxLines($move);

        $this->syncRoundingLines($move);

        $this->syncPaymentTermLines($move);
    }

    public function syncTaxLines(Move $move)
    {
        if (! $move->isInvoice(true)) {
            return;
        }

        $move->refresh();

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
                'balance'         => $baseLine['balance'],
            ]);
        }

        foreach ($taxResults['tax_lines_to_delete'] as $taxLineVals) {
            $taxLineVals['record']->delete();
        }

        foreach ($taxResults['tax_lines_to_add'] as $taxLineVals) {
            unset($taxLineVals['tax_ids']);

            $taxMoveLine = MoveLine::create(array_merge($taxLineVals, [
                'display_type' => DisplayType::TAX,
                'move_id'      => $move->id,
            ]));

            $taxMoveLine->computeCreditAndDebit();

            $taxMoveLine->save();
        }

        foreach ($taxResults['tax_lines_to_update'] as $taxLineVals) {
            unset($taxLineVals['tax_ids']);

            $taxLineVals['record']->update($taxLineVals);

            $taxLineVals['record']->computeCreditAndDebit();

            $taxLineVals['record']->save();
        }
    }

    public function syncRoundingLines(Move $move)
    {
        if ($move->state === MoveState::POSTED) {
            return;
        }

        $computeCashRounding = function ($move, $totalAmountCurrency) {
            $difference = $move->invoiceCashRounding->computeDifference($move->currency, $totalAmountCurrency);

            if ($move->currency->id === $move->company->currency->id) {
                $diffAmountCurrency = $diffBalance = $difference;
            } else {
                $diffAmountCurrency = $difference;

                $diffBalance = $move->currency->convert(
                    $diffAmountCurrency, 
                    $move->company->currency, 
                    $move->company, 
                    $move->invoice_date ?? $move->date
                );
            }
            
            return [$diffBalance, $diffAmountCurrency];
        };

        $applyCashRounding = function ($move, $diffBalance, $diffAmountCurrency, $cashRoundingLine) {
            $roundingLineVals = [
                'balance' => $diffBalance,
                'amount_currency' => $diffAmountCurrency,
                'partner_id' => $move->partner_id,
                'move_id' => $move->id,
                'currency_id' => $move->currency_id,
                'company_id' => $move->company_id,
                'company_currency_id' => $move->company->currency_id,
                'display_type' => DisplayType::ROUNDING,
            ];

            if ($move->invoiceCashRounding->strategy === 'biggest_tax') {
                $biggestTaxLine = null;

                $taxLines = $move->lines->filter(function ($line) {
                    return $line->tax_repartition_line_id !== null;
                });
                
                foreach ($taxLines as $taxLine) {
                    if (! $biggestTaxLine || abs($taxLine->balance) > abs($biggestTaxLine->balance)) {
                        $biggestTaxLine = $taxLine;
                    }
                }

                if (! $biggestTaxLine) {
                    return null;
                }

                $roundingLineVals['name'] = "Tax Rounding ({$biggestTaxLine->name})";

                $roundingLineVals['account_id'] = $biggestTaxLine->account_id;

                $roundingLineVals['tax_repartition_line_id'] = $biggestTaxLine->tax_repartition_line_id;

                $roundingLineVals['tax_ids'] = $biggestTaxLine->taxes->pluck('id')->toArray();
            } elseif ($move->invoiceCashRounding->strategy === 'add_invoice_line') {
                if ($diffBalance > 0.0 && $move->invoiceCashRounding->loss_account_id) {
                    $accountId = $move->invoiceCashRounding->loss_account_id;
                } else {
                    $accountId = $move->invoiceCashRounding->profit_account_id;
                }
                
                $roundingLineVals['name'] = $move->invoiceCashRounding->name;

                $roundingLineVals['account_id'] = $accountId;

                $roundingLineVals['tax_ids'] = [];
            }

            if ($cashRoundingLine) {
                $cashRoundingLine->update($roundingLineVals);

                return $cashRoundingLine;
            } else {
                return MoveLine::create($roundingLineVals);
            }
        };

        $existingCashRoundingLine = $move->lines->filter(function ($line) {
            return $line->display_type === DisplayType::ROUNDING;
        })->first();

        if (! $move->invoiceCashRounding) {
            if ($existingCashRoundingLine) {
                $existingCashRoundingLine->delete();
            }

            return;
        }

        if ($move->invoiceCashRounding && $existingCashRoundingLine) {
            $strategy = $move->invoiceCashRounding->strategy;

            $oldStrategy = $existingCashRoundingLine->tax_line_id ? 'biggest_tax' : 'add_invoice_line';
            
            if ($strategy !== $oldStrategy) {
                $existingCashRoundingLine->delete();
                
                $existingCashRoundingLine = null;
            }
        }

        $othersLines = $move->lines->filter(function ($line) {
            return !in_array($line->account->account_type, [AccountType::ASSET_RECEIVABLE, AccountType::LIABILITY_PAYABLE]);
        });
        
        if ($existingCashRoundingLine) {
            $othersLines = $othersLines->reject(function ($line) use ($existingCashRoundingLine) {
                return $line->id === $existingCashRoundingLine->id;
            });
        }
        
        $totalAmountCurrency = $othersLines->sum('amount_currency');

        [$diffBalance, $diffAmountCurrency] = $computeCashRounding($move, $totalAmountCurrency);

        if ($move->currency->isZero($diffBalance) && $move->currency->isZero($diffAmountCurrency)) {
            if ($existingCashRoundingLine) {
                $existingCashRoundingLine->delete();
            }

            return;
        }

        if ($existingCashRoundingLine) {
            $balanceCompare = float_compare(
                $existingCashRoundingLine->balance, 
                $diffBalance, 
                precisionRounding: $move->currency->rounding
            );

            $amountCompare = float_compare(
                $existingCashRoundingLine->amount_currency, 
                $diffAmountCurrency, 
                precisionRounding: $move->currency->rounding
            );
            
            if ($balanceCompare === 0 && $amountCompare === 0) {
                return;
            }
        }

        $applyCashRounding($move, $diffBalance, $diffAmountCurrency, $existingCashRoundingLine);
    }

    public function syncPaymentTermLines(Move $move)
    {
        if (! $move->isInvoice(true)) {
            return;
        }

        $move->refresh();

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
                'key'    => $key,
                'values' => [
                    'balance'                  => $data['balance'],
                    'amount_currency'          => $data['amount_currency'],
                    'discount_date'            => $data['discount_date'],
                    'discount_balance'         => $data['discount_balance'],
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

                $existingLines[$keyStr]->computeAmountResidual();

                $existingLines[$keyStr]->save();
            } else {
                $moveLine = MoveLine::create(array_merge($attributes, [
                    'display_type' => DisplayType::PAYMENT_TERM,
                ]));

                $moveLine->computeCreditAndDebit();

                $moveLine->computeAmountResidual();

                $moveLine->save();
            }
        }
    }

    protected function getRoundedBaseAndTaxLines(Move $move, $roundFromTaxLines = true)
    {
        $baseMoveLines = $move->lines->where('display_type', DisplayType::PRODUCT);

        $baseLines = [];

        foreach ($baseMoveLines as $line) {
            $baseLines[] = $this->prepareProductBaseLineForTaxesComputation($line);
        }

        $taxLines = [];

        $cashRoundingMoveLines = $move->lines
            ->where('display_type', DisplayType::ROUNDING)
            ->whereNull('tax_repartition_line_id');

        foreach ($cashRoundingMoveLines as $line) {
            $baseLines[] = $this->prepareCashRoundingBaseLineForTaxesComputation($move, $line);
        }

        $baseLines = TaxFacade::addTaxDetailsInBaseLines($baseLines, $move->company);

        $taxMoveLines = $move->lines->whereNotNull('tax_repartition_line_id');

        foreach ($taxMoveLines as $taxLine) {
            $taxLines[] = TaxFacade::prepareTaxLineForTaxesComputation($taxLine, sign: $move->direction_sign);
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

            // $sign = $move->direction_sign;
            
            [$baseLines, $taxLines] = $this->getRoundedBaseAndTaxLines($move, false);

            $baseLines = TaxFacade::addAccountingDataInBaseLinesTaxDetails($baseLines, $move->company, $move->always_tax_exigible);

            $taxResults = TaxFacade::prepareTaxLines($baseLines, $move->company);

            foreach ($taxResults['base_lines_to_update'] as $baseLine) {
                $untaxedAmountCurrency += $sign * abs($baseLine['amount_currency']);

                $untaxedAmount += $sign * abs($baseLine['balance']);
            }

            foreach ($taxResults['tax_lines_to_add'] as $taxLineVals) {
                $taxAmountCurrency += $sign * abs($taxLineVals['amount_currency']);

                $taxAmount += $sign * abs($taxLineVals['balance']);
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
                        'move_id'       => $move->id,
                        'date_maturity' => $termLine['date']?->toDateString(),
                        'discount_date' => $invoicePaymentTerms['discount_date']?->toDateString(),
                    ];

                    $values = [
                        'balance'                  => $termLine['company_amount'],
                        'amount_currency'          => $termLine['foreign_amount'],
                        'discount_date'            => $invoicePaymentTerms['discount_date'] ?? null,
                        'discount_balance'         => $invoicePaymentTerms['discount_balance'] ?? 0.0,
                        'discount_amount_currency' => $invoicePaymentTerms['discount_amount_currency'] ?? 0.0,
                    ];

                    $keyStr = json_encode($key);

                    if (! isset($neededTerms[$keyStr])) {
                        $neededTerms[$keyStr] = array_merge($key, $values);
                    } else {
                        $neededTerms[$keyStr]['balance'] += $values['balance'];

                        $neededTerms[$keyStr]['amount_currency'] += $values['amount_currency'];
                    }
                }
            } else {
                $key = [
                    'move_id'                  => $move->id,
                    'date_maturity'            => $move->invoice_date_due?->toDateString(),
                    'discount_date'            => false,
                    'discount_balance'         => 0.0,
                    'discount_amount_currency' => 0.0,
                ];

                $keyStr = json_encode($key);

                $neededTerms[$keyStr] = array_merge($key, [
                    'balance'         => $untaxedAmount + $taxAmount,
                    'amount_currency' => $untaxedAmountCurrency + $taxAmountCurrency,
                ]);
            }
        }

        return $neededTerms;
    }

    public function createPayments(PaymentRegister $paymentRegister)
    {
        $batches = [];
        
        foreach ($paymentRegister->batches as $batch) {
            $batchAccount = $paymentRegister->getBatchAccount($batch);

            if ($paymentRegister->require_partner_bank_account && (! $batchAccount || ! $batchAccount->can_send_money)) {
                continue;
            }

            $batches[] = $batch;
        }

        if (empty($batches)) {
            throw new \Exception(
                "To record payments with " . $paymentRegister->paymentMethodLine->name . ", the recipient bank account must be manually validated. You should go on the partner bank account in order to validate it."
            );
        }

        $firstBatchResult = $batches[0];

        $editMode = $paymentRegister->is_single_batch
            && (
                count($firstBatchResult['lines']) == 1
                || $paymentRegister->group_payment
            );

        $paymentsToProcess = [];

        if ($editMode) {
            $paymentVals = $this->createPaymentValsFromFirstBatch($paymentRegister, $firstBatchResult);

            $paymentsToProcessValues = [
                'create_vals' => $paymentVals,
                'to_reconcile' => $firstBatchResult['lines'],
                'batch' => $firstBatchResult,
            ];

            if ($paymentRegister->writeoff_is_exchange_account && $paymentRegister->currency_id == $paymentRegister->company_currency_id) {
                $totalBatchResidual = $firstBatchResult['lines']->sum('amount_residual_currency');

                $paymentsToProcessValues['rate'] = $paymentRegister->amount ? abs($totalBatchResidual / $paymentRegister->amount) : 0.0;
            }

            $paymentsToProcess[] = $paymentsToProcessValues;
        } else {
            if (! $paymentRegister->group_payment) {
                $linesToPay = in_array($paymentRegister->installments_mode, ['next', 'overdue', 'before_date']) 
                    ? $paymentRegister->getTotalAmountsToPay($batches)['lines'] 
                    : $paymentRegister->lines;

                $newBatches = [];
                
                foreach ($batches as $batchResult) {
                    foreach ($batchResult['lines'] as $line) {
                        if (! $linesToPay->contains($line)) {
                            continue;
                        }

                        $newBatches[] = array_merge($batchResult, [
                            'payment_values' => array_merge($batchResult['payment_values'], [
                                'payment_type' => $line->balance > 0 ? 'inbound' : 'outbound'
                            ]),
                            'lines' => $line,
                        ]);
                    }
                }

                $batches = $newBatches;
            }

            foreach ($batches as $batchResult) {
                $paymentsToProcess[] = [
                    'create_vals' => $this->createPaymentValsFromBatch($paymentRegister, $batchResult),
                    'to_reconcile' => $batchResult['lines'],
                    'batch' => $batchResult,
                ];
            }
        }

        $this->initiatePayments($paymentRegister, $paymentsToProcess, $editMode);

        $this->postPayments($paymentsToProcess, $editMode);

        $this->reconcilePayments($paymentsToProcess, $editMode);
    }

    public function createPaymentValsFromFirstBatch($paymentRegister, $batchResult)
    {
        $paymentVals = [
            'date' => $paymentRegister->payment_date,
            'amount' => $paymentRegister->amount,
            'payment_type' => $paymentRegister->payment_type,
            'partner_type' => $paymentRegister->partner_type,
            'memo' => $paymentRegister->communication,
            'journal_id' => $paymentRegister->journal_id,
            'company_id' => $paymentRegister->company_id,
            'currency_id' => $paymentRegister->currency_id,
            'partner_id' => $paymentRegister->partner_id,
            'partner_bank_id' => $paymentRegister->partner_bank_id,
            'payment_method_line_id' => $paymentRegister->payment_method_line_id,
            'destination_account_id' => $paymentRegister->lines[0]->account_id,
            'write_off_line_vals' => [],
        ];

        if ($paymentRegister->payment_difference_handling == 'reconcile') {
            if (! $paymentRegister->currency->isZero($paymentRegister->payment_difference)) {
                if ($paymentRegister->writeoff_is_exchange_account) {
                    if ($paymentRegister->currency_id != $paymentRegister->company_currency_id) {
                        $paymentVals['force_balance'] = $batchResult['lines']->sum('amount_residual');
                    }
                } else {
                    if ($paymentRegister->payment_type == 'inbound') {
                        $writeOffAmountCurrency = $paymentRegister->payment_difference;
                    } else {
                        $writeOffAmountCurrency = -$paymentRegister->payment_difference;
                    }

                    $paymentVals['write_off_line_vals'][] = [
                        'name' => 'Write Off',
                        'account_id' => $paymentRegister->writeoff_account_id,
                        'partner_id' => $paymentRegister->partner_id,
                        'currency_id' => $paymentRegister->currency_id,
                        'amount_currency' => $writeOffAmountCurrency,
                        'balance' => $paymentRegister->currency->convert(
                            $writeOffAmountCurrency, 
                            $paymentRegister->company->currency, 
                            $paymentRegister->company, 
                            $paymentRegister->payment_date
                        ),
                    ];
                }
            }
        }

        return $paymentVals;
    }

    public function createPaymentValsFromBatch($paymentRegister, $batch)
    {
        $batchValues = $paymentRegister->getValuesFromBatch($batch);

        if ($batchValues['payment_type'] == 'inbound') {
            $partnerBankId = $paymentRegister->journal->bank_account_id;
        } else {
            $partnerBankId = $batch['payment_values']['partner_bank_id'];
        }

        $paymentMethodLine = $paymentRegister->paymentMethodLine;

        if ($batchValues['payment_type'] != $paymentMethodLine->payment_type) {
            $paymentMethodLine = $paymentRegister->journal->getAvailablePaymentMethodLines($batchValues['payment_type'])->first();
        }

        $paymentVals = [
            'date' => $paymentRegister->payment_date,
            'amount' => $batchValues['source_amount_currency'],
            'payment_type' => $batchValues['payment_type'],
            'partner_type' => $batchValues['partner_type'],
            'memo' => $paymentRegister->getCommunication($batch['lines']),
            'journal_id' => $paymentRegister->journal_id,
            'company_id' => $paymentRegister->company_id,
            'currency_id' => $batchValues['source_currency_id'],
            'partner_id' => $batchValues['partner_id'],
            'payment_method_line_id' => $paymentMethodLine,
            'destination_account_id' => $batch['lines'][0]->account_id,
            'write_off_line_vals' => [],
        ];

        if ($partnerBankId) {
            $paymentVals['partner_bank_id'] = $partnerBankId;
        }

        return $paymentVals;
    }

    public function initiatePayments($paymentRegister, &$paymentsToProcess, $editMode = false)
    {
        $accountingInstalled = (new Move)->getInvoiceInPaymentState() == 'in_payment';

        foreach ($paymentsToProcess as $index => $processItem) {
            $vals = $processItem['create_vals'];
            
            $forceBalanceVals = $vals['force_balance'] ?? null;

            $writeOffLineVals = $vals['write_off_line_vals'] ?? null;

            $lines = $vals['lines'] ?? null;

            unset($vals['write_off_line_vals'], $vals['force_balance'], $vals['lines']);

            //TODO: change this to create after testing
            $vals['payment'] = $payment = Payment::updateOrCreate($vals);

            if (
                ! $accountingInstalled
                && ! $payment->outstanding_account_id
            ) {
                $payment->update([
                    'outstanding_account_id' => $payment->getOutstandingAccount($payment->payment_type)->id
                ]);
            }

            if (
                ! $writeOffLineVals !== null
                || ! $forceBalanceVals !== null
                || ! $lines !== null
            ) {
                $payment->generateJournalEntry($writeOffLineVals, $forceBalanceVals, $lines);

                $payment->refresh();

                $moveVals = collect($vals)
                    ->filter(function ($value, $fieldName) use($payment) {
                        return in_array($fieldName, $payment->moveRelatedFields);
                    })
                    ->toArray();
                
                if (! empty($moveVals)) {
                    $payment->move->update($moveVals);
                }
            }

            $paymentsToProcess[$index]['payment'] = $payment;

            if ($editMode && $payment->move_id) {
                $lines = $processItem['to_reconcile'];

                if ($payment->currency_id == $lines->first()->currency_id) {
                    continue;
                }

                [$liquidityLines, $counterpartLines, $writeoffLines] = $payment->seekForLines();
                
                $sourceBalance = abs($lines->sum('amount_residual'));

                $paymentRate = $liquidityLines[0]->balance ? $liquidityLines[0]->amount_currency / $liquidityLines[0]->balance : 0.0;

                $sourceBalanceConverted = abs($sourceBalance) * $paymentRate;

                $paymentBalance = abs($counterpartLines->sum('balance'));

                $paymentAmountCurrency = abs($counterpartLines->sum('amount_currency'));
                
                if (! $payment->currency->isZero($sourceBalanceConverted - $paymentAmountCurrency)) {
                    continue;
                }

                $deltaBalance = $sourceBalance - $paymentBalance;

                if ($paymentRegister->companyCurrency->isZero($deltaBalance)) {
                    continue;
                }

                $debitLines = $liquidityLines->merge($counterpartLines)->filter(fn($line) => $line->debit > 0);

                $creditLines = $liquidityLines->merge($counterpartLines)->filter(fn($line) => $line->credit > 0);

                if (
                    $debitLines->isNotEmpty()
                    && $creditLines->isNotEmpty()
                ) {
                    //TODO: handle write-off lines if any
                    $payment->move->update([
                        'line_ids' => [
                            ['id' => $debitLines[0]->id, 'debit' => $debitLines[0]->debit + $deltaBalance],
                            ['id' => $creditLines[0]->id, 'credit' => $creditLines[0]->credit + $deltaBalance],
                        ]
                    ]);
                }
            }
        }
    }

    public function postPayments($paymentsToProcess, $editMode = false)
    {
        foreach ($paymentsToProcess as $vals) {
            $this->postPayment($vals['payment']);
        }
    }

    public function postPayment(Payment $payment)
    {
        if ($payment->require_partner_bank_account && ! $payment->partnerBank->can_send_money) {
            throw new \Exception(__(
                "To record payments with :method_name, the recipient bank account must be manually validated. " .
                "You should go on the partner bank account of :partner in order to validate it.",
                [
                    'method_name' => $payment->paymentMethodLine->name,
                    'partner' => $payment->partner->display_name,
                ]
            ));
        }

        if ($payment->outstandingAccount->account_type === AccountType::ASSET_CASH) {
            $payment->state = 'paid';

            $payment->save();
        }

        if (in_array($payment->state, [null, 'draft', 'in_process'])) {
            $payment->state = 'in_process';

            $payment->save();
        }
    }

    public function reconcilePayments($paymentsToProcess, $editMode = false)
    {
        foreach ($paymentsToProcess as $values) {
            $payment = $values['payment'];

            $paymentLines = $payment->move->lines->filter(function($line) {
                return $line->parent_state == 'posted' 
                    && in_array($line->account_type, [AccountType::ASSET_RECEIVABLE, AccountType::LIABILITY_PAYABLE])
                    && ! $line->reconciled;
            });

            $lines = $values['to_reconcile'];

            $extraContext = isset($values['rate']) ? ['forced_rate_from_register_payment' => $values['rate']] : [];

            foreach ($paymentLines->pluck('account_id')->unique() as $accountId) {
                $paymentLines->merge($lines)
                    ->withContext($extraContext)
                    ->filter(function($line) use ($accountId) {
                        return $line->account_id == $accountId
                            && !$line->reconciled
                            && $line->parent_state == 'posted';
                    })
                    ->reconcile();
            }
            
            foreach ($lines as $line) {
                $line->move->matchedPayments()->attach($payment->id);
            }
        }
    }
}
