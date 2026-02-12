<?php

namespace Webkul\Account;

use Exception;
use Filament\Notifications\Notification;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Webkul\Account\Enums\AccountType;
use Webkul\Account\Enums\DisplayType;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Enums\PaymentState;
use Webkul\Account\Enums\PaymentStatus;
use Webkul\Account\Enums\PaymentType;
use Webkul\Account\Events\MoveCancelled;
use Webkul\Account\Events\MoveConfirmed;
use Webkul\Account\Events\MoveDrafted;
use Webkul\Account\Events\MovePaid;
use Webkul\Account\Events\MoveReversed;
use Webkul\Account\Facades\Tax as TaxFacade;
use Webkul\Account\Mail\Invoice\Actions\InvoiceEmail;
use Webkul\Account\Models\Account;
use Webkul\Account\Models\FullReconcile;
use Webkul\Account\Models\Move as AccountMove;
use Webkul\Account\Models\MoveLine;
use Webkul\Account\Models\PartialReconcile;
use Webkul\Account\Models\Partner;
use Webkul\Account\Models\Payment;
use Webkul\Account\Models\PaymentRegister;
use Webkul\Account\Settings\DefaultAccountSettings;
use Webkul\Support\Services\EmailService;

class AccountManager
{
    public function cancelMove(AccountMove $record): AccountMove
    {
        $record->state = MoveState::CANCEL;

        $record->save();

        $record = $this->computeAccountMove($record);

        $record->refresh();

        foreach ($record->lines as $line) {
            $line->update(['parent_state' => MoveState::CANCEL]);
        }

        MoveCancelled::dispatch($record);

        return $record;
    }

    public function confirmMove(AccountMove $record): AccountMove
    {
        $this->isConfirmAllowedForMove($record);

        if ($record->reversedEntry?->state == MoveState::POSTED) {
            $this->reconcileReversedMoves(collect([$record->reversedEntry]), [$record]);

            $tempNumbers = $record->lines
                ->filter(
                    fn ($line) => ! empty($line->matching_number) &&
                        str_starts_with($line->matching_number, 'I')
                )
                ->pluck('matching_number')
                ->unique()
                ->values();

            if ($tempNumbers->isNotEmpty()) {
                $grouped = MoveLine::with(['move', 'account'])
                    ->whereIn('matching_number', $tempNumbers)
                    ->get()
                    ->groupBy(fn ($line) => $line->matching_number.':'.$line->account_id);

                foreach ($grouped as $groupLines) {
                    if (! $groupLines->every(fn ($line) => $line->move->state === MoveState::POSTED)) {
                        continue;
                    }

                    $account = $groupLines->first()->account;

                    if (! $account->reconcile) {
                        $account->update(['reconcile' => true]);
                    }

                    $this->reconcile($groupLines);
                }
            }
        }

        $record->state = MoveState::POSTED;

        $record->posted_before = true;

        $record->save();

        $record = $this->computeAccountMove($record);

        $record->refresh();

        foreach ($record->lines as $line) {
            $line->update(['parent_state' => MoveState::POSTED]);
        }

        if ($record->isSaleDocument()) {
            $record->partner?->update(['customer_rank' => DB::raw('COALESCE(customer_rank, 0) + 1')]);
        } elseif ($record->isPurchaseDocument()) {
            $record->partner?->update(['supplier_rank' => DB::raw('COALESCE(supplier_rank, 0) + 1')]);
        } elseif ($record->move_type == MoveType::ENTRY) {
            $record->lines
                ->filter(fn ($line) => $line->partner_id && $line->account->account_type == AccountType::ASSET_RECEIVABLE)
                ->pluck('partner')
                ->unique()
                ->each(function ($partner) {
                    $partner?->update(['customer_rank' => DB::raw('COALESCE(customer_rank, 0) + 1')]);
                });

            $record->lines
                ->filter(fn ($line) => $line->partner_id && $line->account->account_type == AccountType::LIABILITY_PAYABLE)
                ->pluck('partner')
                ->unique()
                ->each(function ($partner) {
                    $partner?->update(['supplier_rank' => DB::raw('COALESCE(supplier_rank, 0) + 1')]);
                });
        }

        MoveConfirmed::dispatch($record);

        return $record;
    }

    public function setAsCheckedMove(AccountMove $record): AccountMove
    {
        $record->checked = true;

        $record->save();

        $record = $this->computeAccountMove($record);

        return $record;
    }

    public function resetToDraftMove(AccountMove $record): AccountMove
    {
        if (! in_array($record->state, [MoveState::CANCEL, MoveState::POSTED])) {
            throw new Exception('Only posted/cancelled journal entries can be reset to draft.');
        }

        $partialReconcileExchangeMoveIds = PartialReconcile::where('exchange_move_id', $record->id)->pluck('id')->unique();

        $fullReconcileExchangeMoveIds = FullReconcile::where('exchange_move_id', $record->id)->pluck('id')->unique();

        if ($partialReconcileExchangeMoveIds->merge($fullReconcileExchangeMoveIds)->isNotEmpty()) {
            throw new Exception('You cannot reset to draft an exchange difference journal entry.');
        }

        $record->lines->each(function ($line) {
            $line->matchedDebits->each(fn ($partial) => $this->unReconcile($partial));

            $line->matchedCredits->each(fn ($partial) => $this->unReconcile($partial));
        });

        $record->state = MoveState::DRAFT;

        $record->payment_state = PaymentState::NOT_PAID;

        $record->save();

        $record = $this->computeAccountMove($record);

        $record->save();

        $record->refresh();

        foreach ($record->lines as $line) {
            $line->update(['parent_state' => MoveState::DRAFT]);
        }

        MoveDrafted::dispatch($record);

        return $record;
    }

    public function printAndSendMove(AccountMove $record, array $data): AccountMove
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

    public function computeMoveTotals(AccountMove $move): AccountMove
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

    public function syncDynamicLines(AccountMove $move)
    {
        $this->syncTaxLines($move);

        $this->syncRoundingLines($move);

        $this->syncPaymentTermLines($move);
    }

    public function syncTaxLines(AccountMove $move)
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
            $move->company
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

    public function syncRoundingLines(AccountMove $move)
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
                'balance'             => $diffBalance,
                'debit'               => $diffBalance > 0.0 ? $diffBalance : 0.0,
                'credit'              => $diffBalance < 0.0 ? -$diffBalance : 0.0,
                'amount_currency'     => $diffAmountCurrency,
                'partner_id'          => $move->partner_id,
                'move_id'             => $move->id,
                'currency_id'         => $move->currency_id,
                'company_id'          => $move->company_id,
                'company_currency_id' => $move->company->currency_id,
                'display_type'        => DisplayType::ROUNDING,
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
            return ! in_array($line->account->account_type, [AccountType::ASSET_RECEIVABLE, AccountType::LIABILITY_PAYABLE]);
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

        $applyCashRounding($move, $diffBalance, $diffAmountCurrency, $existingCashRoundingLine);
    }

    public function syncPaymentTermLines(AccountMove $move)
    {
        if (! $move->isInvoice(true)) {
            return;
        }

        $move->refresh();

        $neededTerms = $this->prepareNeededTerms($move);

        $existingLines = $move->paymentTermLines
            ->keyBy(fn ($line) => json_encode($line->term_key ?? []));

        $neededMapping = collect($neededTerms)->mapWithKeys(function ($data) {
            $key = [
                'move_id'       => $data['move_id'],
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
                $line->matchedDebits()->delete();

                $line->matchedCredits()->delete();

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

    public function getRoundedBaseAndTaxLines(AccountMove $move, $roundFromTaxLines = true)
    {
        $baseLines = $move->lines
            ->where('display_type', DisplayType::PRODUCT)
            ->map(fn ($line) => $this->prepareProductBaseLineForTaxesComputation($line))
            ->all();

        $roundingLines = $move->lines
            ->where('display_type', DisplayType::ROUNDING)
            ->whereNull('tax_repartition_line_id')
            ->map(fn ($line) => TaxFacade::prepareBaseLineForTaxesComputation(
                $line,
                priceUnit: $move->direction_sign * $line->amount_currency,
                quantity: 1.0,
                sign: $move->direction_sign,
                specialMode: 'total_excluded',
                special_type: 'cash_rounding',
                is_refund: in_array($move->move_type, [MoveType::OUT_REFUND, MoveType::IN_REFUND]),
                rate: $move->invoice_currency_rate,
            ))
            ->all();

        $baseLines = TaxFacade::addTaxDetailsInBaseLines(
            array_merge($baseLines, $roundingLines),
            $move->company
        );

        $taxLines = $move->lines
            ->whereNotNull('tax_repartition_line_id')
            ->map(fn ($line) => TaxFacade::prepareTaxLineForTaxesComputation($line, sign: $move->direction_sign))
            ->all();

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

        $rate = $isInvoice
            ? $line->move->invoice_currency_rate
            : (abs($line->balance) ? abs($line->amount_currency) / abs($line->balance) : 0.0);

        return TaxFacade::prepareBaseLineForTaxesComputation(
            $line,
            priceUnit: $isInvoice ? $line->price_unit : $line->amount_currency,
            quantity: $isInvoice ? $line->quantity : 1.0,
            discount: $isInvoice ? $line->discount : 0.0,
            rate: $rate,
            sign: $isInvoice ? $line->move->direction_sign : 1,
            specialMode: $isInvoice ? false : 'total_excluded',
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

            [$baseLines] = $this->getRoundedBaseAndTaxLines($move, false);

            $baseLines = TaxFacade::addAccountingDataInBaseLinesTaxDetails($baseLines, $move->company);

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
        $batches = collect($paymentRegister->batches)
            ->filter(function ($batch) use ($paymentRegister) {
                $batchAccount = $paymentRegister->getBatchAccount($batch);

                return ! $paymentRegister->require_partner_bank_account
                    || ($batchAccount && $batchAccount->can_send_money);
            })
            ->values()
            ->all();

        if (empty($batches)) {
            throw new Exception(
                'To record payments with '.$paymentRegister->paymentMethodLine->name.', the recipient bank account must be manually validated. You should go on the partner bank account in order to validate it.'
            );
        }

        $firstBatchResult = $batches[0];

        $editMode = $paymentRegister->is_single_batch
            && (count($firstBatchResult['lines']) == 1 || $paymentRegister->group_payment);

        $paymentsToProcess = [];

        if ($editMode) {
            $paymentVals = $this->createPaymentValsFromFirstBatch($paymentRegister, $firstBatchResult);

            $paymentsToProcessValues = [
                'create_vals'  => $paymentVals,
                'to_reconcile' => $firstBatchResult['lines'],
                'batch'        => $firstBatchResult,
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

                $batches = collect($batches)
                    ->flatMap(function ($batchResult) use ($linesToPay) {
                        return collect($batchResult['lines'])
                            ->filter(fn ($line) => $linesToPay->contains($line))
                            ->map(fn ($line) => array_merge($batchResult, [
                                'payment_values' => array_merge($batchResult['payment_values'], [
                                    'payment_type' => $line->balance > 0 ? PaymentType::RECEIVE : PaymentType::SEND,
                                ]),
                                'lines' => $line,
                            ]));
                    })
                    ->all();
            }

            foreach ($batches as $batchResult) {
                $paymentsToProcess[] = [
                    'create_vals'  => $this->createPaymentValsFromBatch($paymentRegister, $batchResult),
                    'to_reconcile' => $batchResult['lines'],
                    'batch'        => $batchResult,
                ];
            }
        }

        $this->initiatePayments($paymentRegister, $paymentsToProcess, $editMode);

        $this->postPayments($paymentsToProcess, $editMode);

        $this->reconcilePayments($paymentsToProcess, $editMode);

        $moves = $paymentRegister->lines->map(fn ($line) => $line->move)->unique();

        $moves->each(fn ($move) => MovePaid::dispatch($move));
    }

    public function createPaymentValsFromFirstBatch($paymentRegister, $batchResult)
    {
        $paymentVals = [
            'date'                   => $paymentRegister->payment_date,
            'amount'                 => $paymentRegister->amount,
            'payment_type'           => $paymentRegister->payment_type,
            'partner_type'           => $paymentRegister->partner_type,
            'memo'                   => $paymentRegister->communication,
            'journal_id'             => $paymentRegister->journal_id,
            'company_id'             => $paymentRegister->company_id,
            'currency_id'            => $paymentRegister->currency_id,
            'partner_id'             => $paymentRegister->partner_id,
            'partner_bank_id'        => $paymentRegister->partner_bank_id,
            'payment_method_line_id' => $paymentRegister->payment_method_line_id,
            'destination_account_id' => $paymentRegister->lines[0]->account_id,
            'write_off_line_vals'    => [],
        ];

        if (
            $paymentRegister->payment_difference_handling == 'reconcile'
            && ! $paymentRegister->currency->isZero($paymentRegister->payment_difference)
        ) {
            if ($paymentRegister->writeoff_is_exchange_account) {
                if ($paymentRegister->currency_id != $paymentRegister->company_currency_id) {
                    $paymentVals['force_balance'] = $batchResult['lines']->sum('amount_residual');
                }
            } else {
                $writeOffAmountCurrency = $paymentRegister->payment_type == PaymentType::RECEIVE
                    ? $paymentRegister->payment_difference
                    : -$paymentRegister->payment_difference;

                $paymentVals['write_off_line_vals'][] = [
                    'name'            => 'Write Off',
                    'account_id'      => $paymentRegister->writeoff_account_id,
                    'partner_id'      => $paymentRegister->partner_id,
                    'currency_id'     => $paymentRegister->currency_id,
                    'amount_currency' => $writeOffAmountCurrency,
                    'balance'         => $paymentRegister->currency->convert(
                        $writeOffAmountCurrency,
                        $paymentRegister->company->currency,
                        $paymentRegister->company,
                        $paymentRegister->payment_date
                    ),
                ];
            }
        }

        return $paymentVals;
    }

    public function createPaymentValsFromBatch($paymentRegister, $batch)
    {
        $batchValues = $paymentRegister->getValuesFromBatch($batch);

        $partnerBankId = $batchValues['payment_type'] == PaymentType::RECEIVE
            ? $paymentRegister->journal->bank_account_id
            : data_get($batch, 'payment_values.partner_bank_id');

        $paymentMethodLine = $batchValues['payment_type'] != $paymentRegister->paymentMethodLine->payment_type
            ? $paymentRegister->journal->getAvailablePaymentMethodLines($batchValues['payment_type'])->first()
            : $paymentRegister->paymentMethodLine;

        $paymentVals = [
            'date'                   => $paymentRegister->payment_date,
            'amount'                 => $batchValues['source_amount_currency'],
            'payment_type'           => $batchValues['payment_type'],
            'partner_type'           => $batchValues['partner_type'],
            'memo'                   => $paymentRegister->getCommunication($batch['lines']),
            'journal_id'             => $paymentRegister->journal_id,
            'company_id'             => $paymentRegister->company_id,
            'currency_id'            => $batchValues['source_currency_id'],
            'partner_id'             => $batchValues['partner_id'],
            'payment_method_line_id' => $paymentMethodLine,
            'destination_account_id' => $batch['lines'][0]->account_id,
            'write_off_line_vals'    => [],
        ];

        return array_filter($paymentVals + ['partner_bank_id' => $partnerBankId], fn ($value) => $value !== null);
    }

    public function initiatePayments($paymentRegister, &$paymentsToProcess, $editMode = false)
    {
        $accountingInstalled = (new AccountMove)->getInvoiceInPaymentState() == PaymentState::IN_PAYMENT;

        foreach ($paymentsToProcess as $index => $processItem) {
            $vals = $processItem['create_vals'];

            $forceBalanceVals = data_get($vals, 'force_balance');
            $writeOffLineVals = data_get($vals, 'write_off_line_vals');
            $lines = data_get($vals, 'lines');

            $vals = Arr::except($vals, ['write_off_line_vals', 'force_balance', 'lines']);

            $vals['payment'] = $payment = Payment::create($vals);

            if (! $accountingInstalled && ! $payment->outstanding_account_id) {
                $payment->update([
                    'outstanding_account_id' => $payment->getOutstandingAccount($payment->payment_type)->id,
                ]);
            }

            if (
                ! $writeOffLineVals !== null
                || ! $forceBalanceVals !== null
                || ! $lines !== null
            ) {
                $payment->generateJournalEntry($writeOffLineVals, $forceBalanceVals, $lines);

                $payment->refresh();

                $moveVals = Arr::only($vals, $payment->moveRelatedFields);

                if (filled($moveVals)) {
                    $payment->move->update($moveVals);
                }
            }

            $paymentsToProcess[$index]['payment'] = $payment;

            if (! $editMode || ! $payment->move_id) {
                continue;
            }

            $lines = $processItem['to_reconcile'];

            if ($payment->currency_id == $lines->first()->currency_id) {
                continue;
            }

            [$liquidityLines, $counterpartLines] = $payment->seekForLines();

            $sourceBalance = abs($lines->sum('amount_residual'));

            $paymentRate = $liquidityLines[0]->balance
                ? $liquidityLines[0]->amount_currency / $liquidityLines[0]->balance
                : 0.0;

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

            $mergedLines = $liquidityLines->merge($counterpartLines);

            $debitLines = $mergedLines->filter(fn ($line) => $line->debit > 0);

            $creditLines = $mergedLines->filter(fn ($line) => $line->credit > 0);

            if ($debitLines->isNotEmpty() && $creditLines->isNotEmpty()) {
                $debitLines[0]->update([
                    'debit' => $debitLines[0]->debit + $deltaBalance,
                ]);

                $creditLines[0]->update([
                    'credit' => $creditLines[0]->credit + $deltaBalance,
                ]);
            }
        }
    }

    public function postPayments($paymentsToProcess, $editMode = false)
    {
        foreach ($paymentsToProcess as $vals) {
            $this->postPayment($vals['payment']);

            $this->confirmMove($vals['payment']->move);
        }
    }

    public function postPayment(Payment $payment)
    {
        if ($payment->require_partner_bank_account && ! $payment->partnerBank->can_send_money) {
            throw new Exception(__(
                'To record payments with :method_name, the recipient bank account must be manually validated. '.
                    'You should go on the partner bank account of :partner in order to validate it.',
                [
                    'method_name' => $payment->paymentMethodLine->name,
                    'partner'     => $payment->partner->display_name,
                ]
            ));
        }

        if ($payment->outstandingAccount->account_type === AccountType::ASSET_CASH) {
            $payment->state = PaymentStatus::PAID;

            $payment->save();
        }

        if (in_array($payment->state, [null, PaymentStatus::DRAFT, PaymentStatus::IN_PROCESS])) {
            $payment->state = PaymentStatus::IN_PROCESS;

            $payment->save();
        }

        return $payment;
    }

    public function reconcilePayments($paymentsToProcess, $editMode = false)
    {
        foreach ($paymentsToProcess as $values) {
            $payment = $values['payment']->refresh();

            $paymentLines = $payment->move->lines->filter(function ($line) {
                return $line->parent_state == MoveState::POSTED
                    && in_array($line->account->account_type, [AccountType::ASSET_RECEIVABLE, AccountType::LIABILITY_PAYABLE])
                    && ! $line->reconciled;
            });

            foreach ($paymentLines->pluck('account_id')->unique() as $accountId) {
                $lines = $paymentLines->merge($values['to_reconcile'])
                    ->filter(function ($line) use ($accountId) {
                        return $line->account_id == $accountId
                            && ! $line->reconciled
                            && $line->parent_state == MoveState::POSTED;
                    });

                $this->reconcile($lines);
            }

            foreach ($values['to_reconcile'] as $line) {
                $line->move->matchedPayments()->attach($payment->id);
            }
        }
    }

    public function reconcile($lines)
    {
        $lines->load([
            'account',
            'partner',
            'currency',
            'move',
            'company',
            'matchedDebits',
            'matchedCredits',
        ]);

        $this->isReconciliationAllowedForLines($lines);

        $this->reconcilePlan([$lines]);

        Payment::whereIn('move_id', $lines->pluck('move_id')->unique())
            ->get()
            ->each(function ($payment) {
                $payment->computeReconciliationStatus();

                $payment->save();
            });
    }

    public function reconcileReversedMoves($moves, $reverseMoves)
    {
        foreach ($moves->zip($reverseMoves) as [$move, $reverseMove]) {
            $groupedLines = $move->lines->merge($reverseMove->lines)
                ->reject(fn ($line) => $line->reconciled)
                ->groupBy(fn ($line) => $line->account_id.'|'.($line->currency_id ?? 'null'));

            foreach ($groupedLines as $key => $lines) {
                [$accountId] = explode('|', $key);

                $account = Account::find($accountId);

                if (
                    $account->reconcile
                    || in_array($account->account_type, [AccountType::ASSET_CASH, AccountType::LIABILITY_CREDIT_CARD])
                ) {
                    $this->reconcile($lines);
                }
            }
        }

        return $reverseMoves;
    }

    public function unReconcile($partialReconcile)
    {
        $fullReconcileToUnlink = $partialReconcile->fullReconcile;

        $debitLine = $partialReconcile->debitMove;
        $creditLine = $partialReconcile->creditMove;

        $allReconciledLines = [
            $debitLine->id,
            $creditLine->id,
        ];

        $partialReconcile->delete();

        if ($fullReconcileToUnlink) {
            $fullReconcileToUnlink->delete();
        }

        if ($move = $partialReconcile->exchangeMove) {
            $defaultValues = [[
                'date'      => $move->date,
                'reference' => __('Reversal of: :name', ['name' => $move->name]),
            ]];

            $this->reverseMoves([$move], $defaultValues, true);
        }

        $this->computeAccountMove($debitLine->move->refresh())->save();

        $this->computeAccountMove($creditLine->move->refresh())->save();

        $this->updateMatchingNumbers($allReconciledLines);
    }

    protected function reconcilePlan(array $reconciliationPlan): array
    {
        $allPartialReconciles = [];
        $allFullReconciles = [];

        foreach ($reconciliationPlan as $lines) {
            $plan = $this->prepareReconciliationPlan($lines);

            $results = $this->processReconciliationNode($plan);

            $allPartialReconciles = array_merge($allPartialReconciles, $results['partial_reconciles']);

            $allFullReconciles = array_merge($allFullReconciles, $results['full_reconciles']);
        }

        return [
            'partial_reconciles' => $allPartialReconciles,
            'full_reconciles'    => $allFullReconciles,
        ];
    }

    protected function prepareReconciliationPlan($lines): array
    {
        $sortedLines = $lines->sortBy(function ($line) {
            return [
                $line->date_maturity ?? $line->date,
                $line->currency_id,
                $line->amount_currency,
                $line->balance,
            ];
        });

        $plan = [
            'lines'    => $sortedLines,
            'line_ids' => $sortedLines->pluck('id')->toArray(),
        ];

        if (($currencies = $sortedLines->pluck('currency_id')->unique())->count() > 1) {
            $plan['nodes'] = [];

            foreach ($currencies as $currencyId) {
                $currencyLines = $sortedLines->filter(fn ($line) => $line->currency_id == $currencyId);

                $plan['nodes'][] = [
                    'lines'    => $currencyLines,
                    'line_ids' => $currencyLines->pluck('id')->toArray(),
                ];
            }
        }

        return $plan;
    }

    protected function processReconciliationNode(array $plan): array
    {
        $allPartialReconciles = [];

        $allFullReconciles = [];

        $fullyReconciledIds = [];

        foreach ($plan['nodes'] ?? [] as $childNode) {
            $childResults = $this->processReconciliationNode($childNode);

            $allPartialReconciles = array_merge($allPartialReconciles, $childResults['partial_reconciles']);

            $allFullReconciles = array_merge($allFullReconciles, $childResults['full_reconciles']);

            $fullyReconciledIds = array_merge($fullyReconciledIds, $childResults['fully_reconciled_ids']);
        }

        if (! empty($plan['lines'])) {
            $remainingLines = $plan['lines']->reject(fn ($line) => in_array($line->id, $fullyReconciledIds));

            if ($remainingLines->isNotEmpty()) {
                $lineValuesMapping = $remainingLines->mapWithKeys(fn ($line) => [
                    $line->id => [
                        'line'                     => $line,
                        'amount_residual'          => $line->amount_residual,
                        'amount_residual_currency' => $line->amount_residual_currency,
                    ],
                ])->toArray();

                $results = $this->prepareReconciliationLines(array_values($lineValuesMapping));

                $reconciledLines = [];

                foreach ($results['partials_values_list'] as $partialResult) {
                    $partialValues = $partialResult['partial_values'];

                    $partial = PartialReconcile::create([
                        'debit_move_id'          => $partialValues['debit_move_id'],
                        'credit_move_id'         => $partialValues['credit_move_id'],
                        'amount'                 => $partialValues['amount'],
                        'debit_amount_currency'  => $partialValues['debit_amount_currency'],
                        'credit_amount_currency' => $partialValues['credit_amount_currency'],
                        'company_id'             => $remainingLines->first()->company_id,
                    ]);

                    $allPartialReconciles[] = $partial;

                    $debitLine = MoveLine::find($partialValues['debit_move_id']);
                    $creditLine = MoveLine::find($partialValues['credit_move_id']);

                    $debitLine->amount_residual -= $partialValues['amount'];
                    $debitLine->amount_residual_currency -= $partialValues['debit_amount_currency'];

                    if ($debitLine->companyCurrency->isZero($debitLine->amount_residual)) {
                        $debitLine->reconciled = true;
                    }

                    $debitLine->save();

                    $creditLine->amount_residual += $partialValues['amount'];
                    $creditLine->amount_residual_currency += $partialValues['credit_amount_currency'];

                    if ($creditLine->companyCurrency->isZero($creditLine->amount_residual)) {
                        $creditLine->reconciled = true;
                    }

                    $creditLine->save();

                    $reconciledLines[] = $debitLine->id;
                    $reconciledLines[] = $creditLine->id;

                    $this->computeMoveTotals($debitLine->move->refresh())->save();

                    $this->computeMoveTotals($creditLine->move->refresh())->save();

                    if (! empty($partialResult['exchange_values'])) {
                        $exchangeMoves = $this->createExchangeDifferenceMoves([$partialResult['exchange_values']]);

                        if (! empty($exchangeMoves)) {
                            $partial->update(['exchange_move_id' => $exchangeMoves[0]->id]);
                        }
                    }
                }

                if (! empty($reconciledLines)) {
                    $this->updateMatchingNumbers($reconciledLines);
                }

                $fulls = $this->createFullReconciles($remainingLines);

                $allFullReconciles = array_merge($allFullReconciles, $fulls);

                $fullyReconciledIds = array_merge($fullyReconciledIds, $results['fully_reconciled_line_ids']);
            }
        }

        return [
            'partial_reconciles'   => $allPartialReconciles,
            'full_reconciles'      => $allFullReconciles,
            'fully_reconciled_ids' => $fullyReconciledIds,
        ];
    }

    protected function prepareReconciliationLines(array $valuesList): array
    {
        $debitValuesList = array_values(array_filter(
            $valuesList,
            fn ($values) => $values['line']->balance > 0.0 || $values['line']->amount_currency > 0.0
        ));

        $creditValuesList = array_values(array_filter(
            $valuesList,
            fn ($values) => $values['line']->balance < 0.0 || $values['line']->amount_currency < 0.0
        ));

        $debitIndex = 0;
        $creditIndex = 0;
        $debitValues = null;
        $creditValues = null;
        $fullyReconciledLineIds = [];
        $partialsValuesList = [];

        while (true) {
            if (! $debitValues) {
                if ($debitIndex >= count($debitValuesList)) {
                    break;
                }

                $debitValues = $debitValuesList[$debitIndex++];
            }

            if (! $creditValues) {
                if ($creditIndex >= count($creditValuesList)) {
                    break;
                }

                $creditValues = $creditValuesList[$creditIndex++];
            }

            $results = $this->prepareSinglePartial($debitValues, $creditValues);

            if (! empty($results['partial_values'])) {
                $partialsValuesList[] = $results;
            }

            if ($results['debit_values'] === null) {
                $fullyReconciledLineIds[] = $debitValues['line']->id;

                $debitValues = null;
            } else {
                $debitValues = $results['debit_values'];
            }

            if ($results['credit_values'] === null) {
                $fullyReconciledLineIds[] = $creditValues['line']->id;

                $creditValues = null;
            } else {
                $creditValues = $results['credit_values'];
            }
        }

        return [
            'partials_values_list'      => $partialsValuesList,
            'fully_reconciled_line_ids' => $fullyReconciledLineIds,
        ];
    }

    protected function prepareSinglePartial(array $debitValues, array $creditValues): array
    {
        $debitLine = $debitValues['line'];
        $creditLine = $creditValues['line'];

        $debitCurrency = $debitLine->currency;
        $creditCurrency = $creditLine->currency;
        $companyCurrency = $debitLine->company->currency;

        $remainingDebitAmount = $debitValues['amount_residual'];
        $remainingCreditAmount = $creditValues['amount_residual'];
        $remainingDebitAmountCurrency = $debitValues['amount_residual_currency'];
        $remainingCreditAmountCurrency = $creditValues['amount_residual_currency'];

        $debitAvailableResiduals = $this->prepareLineResidualAmounts($debitValues, $creditCurrency);
        $creditAvailableResiduals = $this->prepareLineResidualAmounts($creditValues, $debitCurrency);

        $reconciliationCurrency = null;

        if (
            $debitCurrency->id != $companyCurrency->id
            && isset($debitAvailableResiduals[$debitCurrency->id])
            && isset($creditAvailableResiduals[$debitCurrency->id])
        ) {
            $reconciliationCurrency = $debitCurrency;
        } elseif (
            $creditCurrency->id != $companyCurrency->id
            && isset($debitAvailableResiduals[$creditCurrency->id])
            && isset($creditAvailableResiduals[$creditCurrency->id])
        ) {
            $reconciliationCurrency = $creditCurrency;
        } else {
            $reconciliationCurrency = $companyCurrency;
        }

        $debitReconValues = $debitAvailableResiduals[$reconciliationCurrency->id] ?? null;
        $creditReconValues = $creditAvailableResiduals[$reconciliationCurrency->id] ?? null;

        $res = [
            'debit_values'  => $debitValues,
            'credit_values' => $creditValues,
        ];

        if (! $debitReconValues) {
            $res['debit_values'] = null;
        }

        if (! $creditReconValues) {
            $res['credit_values'] = null;
        }

        if (! $debitReconValues || ! $creditReconValues) {
            return $res;
        }

        $reconDebitAmount = $debitReconValues['residual'];
        $reconCreditAmount = -$creditReconValues['residual'];

        $minReconAmount = min($reconDebitAmount, $reconCreditAmount);
        $debitFullyMatched = $reconDebitAmount <= $reconCreditAmount;
        $creditFullyMatched = $reconDebitAmount >= $reconCreditAmount;

        if ($reconciliationCurrency->id == $companyCurrency->id) {
            $debitRate = $debitAvailableResiduals[$debitCurrency->id]['rate'] ?? null;
            $creditRate = $creditAvailableResiduals[$creditCurrency->id]['rate'] ?? null;

            $partialAmount = $minReconAmount;

            if ($debitRate) {
                $partialDebitAmountCurrency = $debitCurrency->round($debitRate * $minReconAmount);
                $partialDebitAmountCurrency = min($partialDebitAmountCurrency, $remainingDebitAmountCurrency);
            } else {
                $partialDebitAmountCurrency = 0.0;
            }

            if ($creditRate) {
                $partialCreditAmountCurrency = $creditCurrency->round($creditRate * $minReconAmount);
                $partialCreditAmountCurrency = min($partialCreditAmountCurrency, -$remainingCreditAmountCurrency);
            } else {
                $partialCreditAmountCurrency = 0.0;
            }
        } else {
            $debitRate = $debitReconValues['rate'] ?? null;
            $creditRate = $creditReconValues['rate'] ?? null;

            $partialAmount = $minReconAmount;

            if ($debitRate) {
                $partialDebitAmount = $companyCurrency->round($minReconAmount / $debitRate);
                $partialAmount = min($partialDebitAmount, $remainingDebitAmount);
            }

            if ($creditRate) {
                $partialCreditAmount = $companyCurrency->round($minReconAmount / $creditRate);
                $partialAmount = min($partialCreditAmount, -$remainingCreditAmount);
            }

            $partialDebitAmountCurrency = $minReconAmount;
            $partialCreditAmountCurrency = $minReconAmount;
        }

        $remainingDebitAmount -= $partialAmount;
        $remainingCreditAmount += $partialAmount;
        $remainingDebitAmountCurrency -= $partialDebitAmountCurrency;
        $remainingCreditAmountCurrency += $partialCreditAmountCurrency;

        $res['partial_values'] = [
            'amount'                 => $partialAmount,
            'debit_amount_currency'  => $partialDebitAmountCurrency,
            'credit_amount_currency' => $partialCreditAmountCurrency,
            'debit_move_id'          => $debitLine->id,
            'credit_move_id'         => $creditLine->id,
        ];

        $debitValues['amount_residual'] = $remainingDebitAmount;
        $debitValues['amount_residual_currency'] = $remainingDebitAmountCurrency;
        $creditValues['amount_residual'] = $remainingCreditAmount;
        $creditValues['amount_residual_currency'] = $remainingCreditAmountCurrency;

        if ($debitFullyMatched) {
            $res['debit_values'] = null;
        } else {
            $res['debit_values'] = $debitValues;
        }

        if ($creditFullyMatched) {
            $res['credit_values'] = null;
        } else {
            $res['credit_values'] = $creditValues;
        }

        $exchangeValues = $this->checkExchangeDifference($debitValues, $creditValues, $debitFullyMatched, $creditFullyMatched);

        if ($exchangeValues) {
            $res['exchange_values'] = $exchangeValues;
        }

        return $res;
    }

    protected function prepareLineResidualAmounts(array $lineValues, $counterpartCurrency): array
    {
        $getConversionRate = function ($currency, $line): float {
            if (
                $line->currency_id != $line->company->currency->id
                && ! $line->company->currency->isZero($line->balance)
                && ! $line->currency->isZero($line->amount_currency)
            ) {
                return abs($line->amount_currency / $line->balance);
            }

            return $currency->rate ?? 1.0;
        };

        $line = $lineValues['line'];
        $remainingAmount = $lineValues['amount_residual'];
        $remainingAmountCurrency = $lineValues['amount_residual_currency'];

        $availableResidualPerCurrency = [];

        if (! $line->company->currency->isZero($remainingAmount)) {
            $availableResidualPerCurrency[$line->company->currency->id] = [
                'residual' => $remainingAmount,
                'rate'     => 1.0,
            ];
        }

        if (
            $line->currency->id != $line->company->currency->id
            && ! $line->currency->isZero($remainingAmountCurrency)
        ) {
            $rate = abs($remainingAmountCurrency / $remainingAmount);

            $availableResidualPerCurrency[$line->currency->id] = [
                'residual' => $remainingAmountCurrency,
                'rate'     => $rate,
            ];
        }

        if (
            $counterpartCurrency->id != $line->company->currency->id
            && $line->currency->id == $line->company->currency->id
            && ! $line->company->currency->isZero($remainingAmount)
        ) {
            $rate = $getConversionRate($counterpartCurrency, $line);

            $residualInForeign = $counterpartCurrency->round($remainingAmount * $rate);

            if (! $counterpartCurrency->isZero($residualInForeign)) {
                $availableResidualPerCurrency[$counterpartCurrency->id] = [
                    'residual' => $residualInForeign,
                    'rate'     => $rate,
                ];
            }
        }

        return $availableResidualPerCurrency;
    }

    protected function checkExchangeDifference($debitValues, $creditValues, bool $debitFullyMatched, bool $creditFullyMatched): ?array
    {
        $linesToFix = [];

        if ($debitFullyMatched && $debitValues) {
            $companyCurrency = $debitValues['line']->company->currency;

            if (! $companyCurrency->isZero($debitValues['amount_residual'])) {
                $linesToFix[] = [
                    'line'            => $debitValues['line'],
                    'amount_residual' => $debitValues['amount_residual'],
                ];
            }
        }

        if ($creditFullyMatched && $creditValues) {
            $companyCurrency = $creditValues['line']->company->currency;

            if (! $companyCurrency->isZero($creditValues['amount_residual'])) {
                $linesToFix[] = [
                    'line'            => $creditValues['line'],
                    'amount_residual' => $creditValues['amount_residual'],
                ];
            }
        }

        if (! empty($linesToFix)) {
            return $this->prepareExchangeDifferenceMoveVals($linesToFix);
        }

        return null;
    }

    protected function prepareExchangeDifferenceMoveVals(array $amountsList): ?array
    {
        if (empty($amountsList)) {
            return null;
        }

        $defaultAccountsSettings = new DefaultAccountSettings;

        if (
            ! $journalId = $defaultAccountsSettings->currency_exchange_journal_id
                || ! $expenseAccountId = $defaultAccountsSettings->expense_currency_exchange_account_id
                    || ! $incomeAccountId = $defaultAccountsSettings->income_currency_exchange_account_id
        ) {
            throw new Exception('Exchange difference journal and accounts must be configured');
        }

        $moveValues = [
            'move_type'  => MoveType::ENTRY,
            'date'       => now()->format('Y-m-d'),
            'journal_id' => $journalId,
            'lines'      => [],
        ];

        $toReconcile = [];

        $sequence = 0;

        foreach ($amountsList as $item) {
            $line = $item['line'];

            $amountResidual = $item['amount_residual'];

            if ($line->company->currency->isZero($amountResidual)) {
                continue;
            }

            $moveValues['lines'][] = [
                'name'            => 'Currency exchange rate difference',
                'debit'           => $amountResidual < 0 ? abs($amountResidual) : 0,
                'credit'          => $amountResidual > 0 ? $amountResidual : 0,
                'amount_currency' => 0,
                'account_id'      => $line->account_id,
                'currency_id'     => $line->currency_id,
                'partner_id'      => $line->partner_id,
            ];

            $moveValues['lines'][] = [
                'name'            => 'Currency exchange rate difference',
                'debit'           => $amountResidual > 0 ? $amountResidual : 0,
                'credit'          => $amountResidual < 0 ? abs($amountResidual) : 0,
                'amount_currency' => 0,
                'account_id'      => $amountResidual < 0
                    ? $incomeAccountId
                    : $expenseAccountId,
                'currency_id' => $line->currency_id,
                'partner_id'  => $line->partner_id,
            ];

            $toReconcile[] = [
                'source_line' => $line,
                'sequence'    => $sequence,
            ];

            $sequence += 2;
        }

        return [
            'move_values'  => $moveValues,
            'to_reconcile' => $toReconcile,
        ];
    }

    protected function createFullReconciles($lines): array
    {
        $lines->each->refresh();

        $fullReconciles = [];

        $lines->load([
            'matchedDebits',
            'matchedCredits',
            'company.currency',
            'currency',
        ]);

        $groups = $lines->groupBy(fn ($line) => $line->matching_number ?? 'auto_'.$line->id);

        foreach ($groups as $groupKey => $groupLines) {
            $groupFullyReconciled = $groupLines->every(
                fn ($line) => $line->company->currency->isZero($line->amount_residual)
                    && $line->currency->isZero($line->amount_residual_currency)
            );

            if (! $groupFullyReconciled) {
                continue;
            }

            $invoiceMoves = $groupLines->pluck('move')
                ->filter(fn ($move) => $move->isInvoice(true))
                ->unique();

            $allInvoiceLinesReconciled = $invoiceMoves->every(function ($move) {
                return $move->paymentTermLines->every(
                    fn ($line) => $line->company->currency->isZero($line->amount_residual)
                        && $line->currency->isZero($line->amount_residual_currency)
                );
            });

            $exchangeLinesToFix = $groupLines
                ->filter(
                    fn ($line) => ! $line->company->currency->isZero($line->amount_residual)
                        || ! $line->currency->isZero($line->amount_residual_currency)
                )
                ->map(fn ($line) => [
                    'line'            => $line,
                    'amount_residual' => ! $line->company->currency->isZero($line->amount_residual)
                        ? $line->amount_residual
                        : $line->amount_residual_currency,
                ])
                ->all();

            $exchangeMoveId = null;

            if (! empty($exchangeLinesToFix)) {
                $exchangeDiffValues = $this->prepareExchangeDifferenceMoveVals($exchangeLinesToFix);

                if ($exchangeDiffValues) {
                    $exchangeMoves = $this->createExchangeDifferenceMoves([$exchangeDiffValues]);

                    $exchangeMoveId = $exchangeMoves[0]->id ?? null;
                }
            }

            $partialReconcileIds = $groupLines
                ->flatMap(fn ($line) => $line->matchedCredits->pluck('id')->merge($line->matchedDebits->pluck('id')))
                ->unique()
                ->all();

            $fullReconcile = null;

            if ($allInvoiceLinesReconciled) {
                $fullReconcile = FullReconcile::create(['exchange_move_id' => $exchangeMoveId]);

                PartialReconcile::whereIn('id', $partialReconcileIds)
                    ->update(['full_reconcile_id' => $fullReconcile->id]);

                $fullReconciles[] = $fullReconcile;

                $allReconciledLineIds = PartialReconcile::whereIn('id', $partialReconcileIds)
                    ->get()
                    ->flatMap(fn ($partial) => [$partial->debit_move_id, $partial->credit_move_id])
                    ->unique()
                    ->values();

                $allReconciledLines = MoveLine::whereIn('id', $allReconciledLineIds)->get();

                foreach ($allReconciledLines as $line) {
                    $line->reconciled = true;
                    $line->full_reconcile_id = $fullReconcile->id;
                    $line->matching_number = (string) $fullReconcile->id;

                    $line->save();
                }
            } else {
                foreach ($groupLines as $line) {
                    $line->reconciled = true;
                    $line->save();
                }
            }
        }

        return $fullReconciles;
    }

    public function createExchangeDifferenceMoves(array $exchangeDiffValuesList)
    {
        $exchangeMoves = [];

        foreach ($exchangeDiffValuesList as $exchangeDiffValues) {
            $moveValues = $exchangeDiffValues['move_values'];

            if (empty($moveValues['lines'])) {
                continue;
            }

            $exchangeMove = AccountMove::create($moveValues);
            $exchangeMove->state = 'posted';
            $exchangeMove->save();

            collect($moveValues['lines'])->each(fn ($lineVals) => MoveLine::create($lineVals + ['move_id' => $exchangeMove->id]));

            $exchangeMove = $this->computeAccountMove($exchangeMove);

            foreach ($exchangeDiffValues['to_reconcile'] as $reconcileData) {
                $sourceLine = $reconcileData['source_line'];
                $exchangeLineSequence = $reconcileData['sequence'];

                $exchangeLine = $exchangeMove->lines[$exchangeLineSequence];

                $this->reconcilePlan([$sourceLine->id, $exchangeLine->id]);
            }

            $exchangeMoves[] = $exchangeMove;
        }

        return $exchangeMoves;
    }

    protected function updateMatchingNumbers(array $lineIds): void
    {
        if (empty($lineIds)) {
            return;
        }

        $lines = MoveLine::whereIn('id', $lineIds)->get();

        $allPartials = collect($lines)
            ->flatMap(fn ($line) => $line->matchedDebits->merge($line->matchedCredits))
            ->unique('id')
            ->sortBy('id')
            ->values();

        $groupIdToLineIds = [];
        $lineIdToGroupId = [];

        foreach ($allPartials as $partial) {
            $debitMinId = $lineIdToGroupId[$partial->debit_move_id] ?? null;
            $creditMinId = $lineIdToGroupId[$partial->credit_move_id] ?? null;

            if ($debitMinId && $creditMinId) {
                if ($debitMinId != $creditMinId) {
                    $minMinId = min($debitMinId, $creditMinId);
                    $maxMinId = max($debitMinId, $creditMinId);

                    foreach ($groupIdToLineIds[$maxMinId] as $lineId) {
                        $lineIdToGroupId[$lineId] = $minMinId;
                    }

                    $groupIdToLineIds[$minMinId] = array_merge(
                        $groupIdToLineIds[$minMinId],
                        $groupIdToLineIds[$maxMinId]
                    );

                    unset($groupIdToLineIds[$maxMinId]);
                }
            } elseif ($debitMinId) {
                $groupIdToLineIds[$debitMinId][] = $partial->credit_move_id;
                $lineIdToGroupId[$partial->credit_move_id] = $debitMinId;
            } elseif ($creditMinId) {
                $groupIdToLineIds[$creditMinId][] = $partial->debit_move_id;
                $lineIdToGroupId[$partial->debit_move_id] = $creditMinId;
            } else {
                $groupIdToLineIds[$partial->id] = [$partial->debit_move_id, $partial->credit_move_id];
                $lineIdToGroupId[$partial->debit_move_id] = $partial->id;
                $lineIdToGroupId[$partial->credit_move_id] = $partial->id;
            }
        }

        foreach ($lines as $line) {
            if (isset($lineIdToGroupId[$line->id])) {
                $matchingNumber = $lineIdToGroupId[$line->id];

                if ($line->full_reconcile_id) {
                    $line->matching_number = (string) $line->full_reconcile_id;
                } else {
                    $line->matching_number = 'P'.$matchingNumber;
                }
            } else {
                $line->matching_number = null;
            }

            $line->save();
        }
    }

    public function reverseMoves($moves, $defaultValues = [], $cancel = false)
    {
        if (empty($defaultValues)) {
            $defaultValues = $moves->flatMap(fn ($move) => []);
        }

        if ($cancel) {
            $lines = $moves->flatMap(fn ($move) => $move->lines);

            if ($lines->isNotEmpty()) {
                $lines->each(function ($line) {
                    $line->matchedDebits->each(fn ($partial) => $partial->delete());

                    $line->matchedCredits->each(fn ($partial) => $partial->delete());
                });
            }
        }

        $reverseMoves = collect();

        foreach ($moves as $index => $move) {
            $defaultValues = $defaultValues[$index] ?? [];

            $defaultValues = array_merge($defaultValues, [
                'move_type'          => $move->typeReverseMapping[$move->move_type->value],
                'reversed_entry_id'  => $move->id,
                'partner_id'         => $move->partner_id,
            ]);

            $reverseMove = $move->replicate();
            $reverseMove->fill($defaultValues);
            $reverseMove->save();

            foreach ($move->lines as $line) {
                $newLine = $line->replicate();
                $newLine->move_id = $reverseMove->id;
                $newLine->save();
            }

            $reverseMove = $this->computeAccountMove($reverseMove);

            $reverseMoves->push($reverseMove);
        }

        foreach ($reverseMoves as $reverseMove) {
            foreach ($reverseMove->lines as $line) {
                if ($reverseMove->move_type === MoveType::ENTRY || $line->display_type === DisplayType::COGS) {
                    $line->update([
                        'balance'         => -$line->balance,
                        'amount_currency' => -$line->amount_currency,
                    ]);
                }
            }
        }

        if ($cancel) {
            foreach ($reverseMoves as $reverseMove) {
                $this->confirmMove($reverseMove);
            }
        }

        foreach ($moves as $move) {
            MoveReversed::dispatch($move);
        }

        return $reverseMoves;
    }

    public function isConfirmAllowedForMove(AccountMove &$record)
    {
        if (! $record->partner_id) {
            if ($record->isSaleDocument(true)) {
                throw new Exception(__('accounts::account-manager.post-action-validate.customer-required'));
            } elseif ($record->isPurchaseDocument(true)) {
                throw new Exception(__('accounts::account-manager.post-action-validate.vendor-required'));
            }
        }

        if ($record->partnerBank?->trashed()) {
            throw new Exception(__('accounts::account-manager.post-action-validate.bank-archived'));
        }

        if (float_compare($record->amount_total, 0, precisionRounding: $record->currency->rounding) < 0) {
            throw new Exception(__('accounts::account-manager.post-action-validate.negative-amount'));
        }

        if (! $record->invoice_date) {
            if ($record->isSaleDocument(true)) {
                $record->invoice_date = now();
            } elseif ($record->isPurchaseDocument(true)) {
                throw new Exception(__('accounts::account-manager.post-action-validate.date-required'));
            }
        }

        if (in_array($record->state, [MoveState::POSTED, MoveState::CANCEL])) {
            throw new Exception(__('accounts::account-manager.post-action-validate.draft-state-required'));
        }

        if ($record->lines->isEmpty()) {
            throw new Exception(__('accounts::account-manager.post-action-validate.lines-required'));
        }

        if ($record->lines->some(fn ($line) => $line->account->deprecated)) {
            throw new Exception(__('accounts::account-manager.post-action-validate.account-deprecated'));
        }

        if (! $record->journal) {
            throw new Exception(__('accounts::account-manager.post-action-validate.journal-archived'));
        }

        if (! $record->currency) {
            throw new Exception(__('accounts::account-manager.post-action-validate.currency-archived'));
        }
    }

    public function isReconciliationAllowedForLines($lines)
    {
        if ($lines->isEmpty()) {
            return;
        }

        if ($lines->contains(fn ($line) => $line->reconciled)) {
            throw new Exception(__('You are trying to reconcile some entries that are already reconciled.'));
        }

        if ($lines->contains(fn ($line) => $line->parent_state != MoveState::POSTED)) {
            throw new Exception(__('You can only reconcile posted entries.'));
        }

        $accounts = $lines->pluck('account')->unique();

        if ($accounts->count() > 1) {
            throw new Exception(__(
                'Entries are not from the same account: :accounts',
                ['accounts' => $accounts->pluck('display_name')->implode(', ')]
            ));
        }

        if ($lines->pluck('partner_id')->unique()->count() > 1) {
            throw new Exception('All lines must have the same partner');
        }

        if ($lines->pluck('company')->unique()->count() > 1) {
            throw new Exception(__(
                "Entries don't belong to the same company: :companies",
                ['companies' => $lines->pluck('company')->pluck('display_name')->implode(', ')]
            ));
        }

        $account = $accounts->first();

        if (
            ! $account->reconcile
            && ! in_array($account->account_type, [AccountType::ASSET_CASH, AccountType::LIABILITY_CREDIT_CARD])
        ) {
            throw new Exception(__(
                'Account :account does not allow reconciliation. First change the configuration of this account to allow it.',
                ['account' => $account->display_name]
            ));
        }
    }
}
