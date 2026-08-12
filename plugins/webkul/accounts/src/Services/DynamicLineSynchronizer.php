<?php

namespace Webkul\Account\Services;

use Webkul\Account\Enums\AccountType;
use Webkul\Account\Enums\DisplayType;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Facades\Tax as TaxFacade;
use Webkul\Account\Models\Move as AccountMove;
use Webkul\Account\Models\MoveLine;

class DynamicLineSynchronizer
{
    public function sync(AccountMove $move): void
    {
        $this->syncTaxLines($move);

        $this->syncRoundingLines($move);

        $this->syncPaymentTermLines($move);
    }

    public function syncTaxLines(AccountMove $move): void
    {
        if (! $move->isInvoice(true)) {
            return;
        }

        $move->refresh();

        [$baseLines, $taxLines] = app(MoveCalculator::class)->roundedBaseAndTaxLines($move, false);

        $baseLines = TaxFacade::addAccountingDataToAll($baseLines, $move->company);

        $changes = TaxFacade::buildTaxLineChanges($baseLines, $move->company, $taxLines);

        foreach ($changes['base_lines_to_update'] as $baseLine) {
            $baseLine['record']->update([
                'amount_currency' => $baseLine['amount_currency'],
                'balance'         => $baseLine['balance'],
            ]);
        }

        foreach ($changes['tax_lines_to_delete'] as $taxLine) {
            $taxLine['record']->delete();
        }

        foreach ($changes['tax_lines_to_add'] as $attributes) {
            unset($attributes['tax_ids']);

            $taxLine = MoveLine::create(array_merge($attributes, [
                'display_type' => DisplayType::TAX,
                'move_id'      => $move->id,
            ]));

            $taxLine->computeCreditAndDebit();

            $taxLine->save();
        }

        foreach ($changes['tax_lines_to_update'] as $attributes) {
            unset($attributes['tax_ids']);

            $attributes['record']->update($attributes);

            $attributes['record']->computeCreditAndDebit();

            $attributes['record']->save();
        }
    }

    public function syncRoundingLines(AccountMove $move): void
    {
        if ($move->state === MoveState::POSTED) {
            return;
        }

        $move->load('lines');

        $existing = $move->lines->firstWhere('display_type', DisplayType::ROUNDING);

        if (! $move->invoiceCashRounding) {
            $existing?->delete();

            return;
        }

        if ($existing && $move->invoiceCashRounding->strategy !== ($existing->tax_line_id ? 'biggest_tax' : 'add_invoice_line')) {
            $existing->delete();

            $existing = null;
        }

        $others = $move->lines
            ->filter(fn (MoveLine $line) => $line->account
                && ! in_array($line->account->account_type, [AccountType::ASSET_RECEIVABLE, AccountType::LIABILITY_PAYABLE]))
            ->reject(fn (MoveLine $line) => $existing && $line->id === $existing->id);

        [$diffBalance, $diffAmountCurrency] = $this->cashRoundingDifference($move, $others->sum('amount_currency'));

        if ($move->currency->isZero($diffBalance) && $move->currency->isZero($diffAmountCurrency)) {
            $existing?->delete();

            return;
        }

        $this->applyCashRounding($move, $diffBalance, $diffAmountCurrency, $existing);
    }

    protected function cashRoundingDifference(AccountMove $move, float $totalAmountCurrency): array
    {
        $difference = $move->invoiceCashRounding->computeDifference($move->currency, $totalAmountCurrency);

        if ($move->currency->id === $move->company->currency->id) {
            return [$difference, $difference];
        }

        return [
            $move->currency->convert(
                $difference,
                $move->company->currency,
                $move->company,
                $move->invoice_date ?? $move->date
            ),
            $difference,
        ];
    }

    protected function applyCashRounding(AccountMove $move, float $diffBalance, float $diffAmountCurrency, ?MoveLine $existing): ?MoveLine
    {
        $attributes = [
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
            $biggest = $move->lines
                ->filter(fn (MoveLine $line) => $line->tax_repartition_line_id !== null)
                ->sortByDesc(fn (MoveLine $line) => abs($line->balance))
                ->first();

            if (! $biggest) {
                return null;
            }

            $attributes['name'] = "Tax Rounding ({$biggest->name})";
            $attributes['account_id'] = $biggest->account_id;
            $attributes['tax_repartition_line_id'] = $biggest->tax_repartition_line_id;
            $attributes['tax_ids'] = $biggest->taxes->pluck('id')->toArray();
        } elseif ($move->invoiceCashRounding->strategy === 'add_invoice_line') {
            $attributes['name'] = $move->invoiceCashRounding->name;
            $attributes['account_id'] = $diffBalance > 0.0 && $move->invoiceCashRounding->loss_account_id
                ? $move->invoiceCashRounding->loss_account_id
                : $move->invoiceCashRounding->profit_account_id;
            $attributes['tax_ids'] = [];
        }

        if ($existing) {
            $existing->update($attributes);

            return $existing;
        }

        return MoveLine::create($attributes);
    }

    public function syncPaymentTermLines(AccountMove $move): void
    {
        if (! $move->isInvoice(true)) {
            return;
        }

        $move->refresh();

        $needed = collect($this->buildPaymentTermSchedule($move))->mapWithKeys(function (array $term) {
            $key = [
                'move_id'       => $term['move_id'],
                'date_maturity' => $term['date_maturity'],
                'discount_date' => $term['discount_date'],
            ];

            return [json_encode($key) => [
                'key'    => $key,
                'values' => [
                    'balance'                  => $term['balance'],
                    'amount_currency'          => $term['amount_currency'],
                    'discount_date'            => $term['discount_date'],
                    'discount_balance'         => $term['discount_balance'],
                    'discount_amount_currency' => $term['discount_amount_currency'],
                ],
            ]];
        });

        $existing = $move->paymentTermLines->keyBy(fn (MoveLine $line) => json_encode($line->term_key ?? []));

        foreach ($existing as $key => $line) {
            if ($needed->has($key)) {
                continue;
            }

            $line->matchedDebits()->delete();

            $line->matchedCredits()->delete();

            $line->delete();

            $existing->forget($key);
        }

        foreach ($needed as $key => $term) {
            $attributes = array_merge($term['values'], $term['key']);

            $line = $existing->get($key);

            if ($line) {
                $line->update($attributes);
            } else {
                $line = MoveLine::create(array_merge($attributes, ['display_type' => DisplayType::PAYMENT_TERM]));
            }

            $line->computeCreditAndDebit();

            $line->computeAmountResidual();

            $line->save();
        }
    }

    public function buildPaymentTermSchedule(AccountMove $move): array
    {
        if (! $move->isInvoice(true) || ! $move->invoiceLines()->exists()) {
            return [];
        }

        $sign = $move->isInbound(true) ? 1 : -1;

        [$baseLines] = app(MoveCalculator::class)->roundedBaseAndTaxLines($move, false);

        $baseLines = TaxFacade::addAccountingDataToAll($baseLines, $move->company);

        $changes = TaxFacade::buildTaxLineChanges($baseLines, $move->company);

        $directionSign = $move->direction_sign;

        $untaxedAmount = $untaxedAmountCurrency = 0.0;

        $taxAmount = $taxAmountCurrency = 0.0;

        foreach ($changes['base_lines_to_update'] as $baseLine) {
            $untaxedAmountCurrency += $sign * $directionSign * $baseLine['amount_currency'];
            $untaxedAmount += $sign * $directionSign * $baseLine['balance'];
        }

        foreach ($changes['tax_lines_to_add'] as $taxLine) {
            $taxAmountCurrency += $sign * $directionSign * $taxLine['amount_currency'];
            $taxAmount += $sign * $directionSign * $taxLine['balance'];
        }

        $biggestTaxRoundingLines = $move->lines
            ->where('display_type', DisplayType::ROUNDING)
            ->whereNotNull('tax_repartition_line_id');

        foreach ($biggestTaxRoundingLines as $roundingLine) {
            $taxAmountCurrency += $sign * $directionSign * $roundingLine->amount_currency;
            $taxAmount += $sign * $directionSign * $roundingLine->balance;
        }

        if (! $move->invoice_payment_term_id) {
            return $this->singleDueDateSchedule($move, $untaxedAmount + $taxAmount, $untaxedAmountCurrency + $taxAmountCurrency);
        }

        return $this->instalmentSchedule(
            $move,
            $move->invoicePaymentTerm->computeTerms(
                dateRef: $move->invoice_date ?? $move->date ?? now(),
                currency: $move->currency,
                taxAmountCurrency: $taxAmountCurrency,
                taxAmount: $taxAmount,
                untaxedAmountCurrency: $untaxedAmountCurrency,
                untaxedAmount: $untaxedAmount,
                company: $move->company,
                cashRounding: $move->invoiceCashRounding,
                sign: $sign
            )
        );
    }

    protected function singleDueDateSchedule(AccountMove $move, float $balance, float $amountCurrency): array
    {
        $key = [
            'move_id'                  => $move->id,
            'date_maturity'            => $move->invoice_date_due?->toDateString(),
            'discount_date'            => null,
            'discount_balance'         => 0.0,
            'discount_amount_currency' => 0.0,
        ];

        return [
            json_encode($key) => array_merge($key, [
                'balance'         => $balance,
                'amount_currency' => $amountCurrency,
            ]),
        ];
    }

    protected function instalmentSchedule(AccountMove $move, array $paymentTerm): array
    {
        $schedule = [];

        foreach ($paymentTerm['lines'] as $termLine) {
            $key = [
                'move_id'       => $move->id,
                'date_maturity' => $termLine['date']?->toDateString(),
                'discount_date' => $paymentTerm['discount_date']?->toDateString(),
            ];

            $keyString = json_encode($key);

            if (isset($schedule[$keyString])) {
                $schedule[$keyString]['balance'] += $termLine['company_amount'];
                $schedule[$keyString]['amount_currency'] += $termLine['foreign_amount'];

                continue;
            }

            $schedule[$keyString] = array_merge($key, [
                'balance'                  => $termLine['company_amount'],
                'amount_currency'          => $termLine['foreign_amount'],
                'discount_date'            => $paymentTerm['discount_date'] ?? null,
                'discount_balance'         => $paymentTerm['discount_balance'] ?? 0.0,
                'discount_amount_currency' => $paymentTerm['discount_amount_currency'] ?? 0.0,
            ]);
        }

        return $schedule;
    }
}
