<?php

namespace Webkul\Account\Services;

use Webkul\Account\Enums\DisplayType;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Facades\Tax as TaxFacade;
use Webkul\Account\Models\Move as AccountMove;
use Webkul\Account\Models\MoveLine;

class MoveCalculator
{
    public function recompute(AccountMove $move): AccountMove
    {
        app(DynamicLineSynchronizer::class)->sync($move);

        $move->refresh();

        foreach ($move->invoiceLines as $line) {
            $this->recomputeLine($line)->save();
        }

        $move = $this->recomputeTotals($move);

        $move->save();

        return $move;
    }

    public function recomputeTotals(AccountMove $move): AccountMove
    {
        $totals = $this->accumulateTotals($move);

        $sign = $move->direction_sign;

        $isEntry = $move->move_type == MoveType::ENTRY;

        $move->amount_untaxed = $sign * $totals['untaxed_currency'];
        $move->amount_tax = $sign * $totals['tax_currency'];
        $move->amount_total = $sign * $totals['total_currency'];
        $move->amount_residual = -$sign * $totals['residual_currency'];

        $move->amount_untaxed_signed = -$totals['untaxed'];
        $move->amount_untaxed_in_currency_signed = -$totals['untaxed_currency'];
        $move->amount_tax_signed = -$totals['tax'];
        $move->amount_residual_signed = $totals['residual'];

        $move->amount_total_signed = $isEntry
            ? abs($totals['total'])
            : -$totals['total'];

        $move->amount_total_in_currency_signed = $isEntry
            ? abs($move->amount_total)
            : -($sign * $move->amount_total);

        return $move;
    }

    protected function accumulateTotals(AccountMove $move): array
    {
        $totals = [
            'untaxed'           => 0.0,
            'untaxed_currency'  => 0.0,
            'tax'               => 0.0,
            'tax_currency'      => 0.0,
            'residual'          => 0.0,
            'residual_currency' => 0.0,
            'total'             => 0.0,
            'total_currency'    => 0.0,
        ];

        $isInvoice = $move->isInvoice(true);

        foreach ($move->lines as $line) {
            if (! $isInvoice) {
                if ($line->display_type == DisplayType::PAYMENT_TERM) {
                    $totals['residual'] += $line->amount_residual;
                    $totals['residual_currency'] += $line->amount_residual_currency;
                } elseif ((float) $line->debit) {
                    $totals['total'] += $line->balance;
                    $totals['total_currency'] += $line->amount_currency;
                }

                continue;
            }

            $isTaxLine = $line->display_type == DisplayType::TAX
                || ($line->display_type == DisplayType::ROUNDING && $line->tax_repartition_line_id);

            if ($isTaxLine) {
                $totals['tax'] += $line->balance;
                $totals['tax_currency'] += $line->amount_currency;
                $totals['total'] += $line->balance;
                $totals['total_currency'] += $line->amount_currency;
            } elseif (in_array($line->display_type, [DisplayType::PRODUCT, DisplayType::ROUNDING])) {
                $totals['untaxed'] += $line->balance;
                $totals['untaxed_currency'] += $line->amount_currency;
                $totals['total'] += $line->balance;
                $totals['total_currency'] += $line->amount_currency;
            } elseif ($line->display_type == DisplayType::PAYMENT_TERM) {
                $totals['residual'] += $line->amount_residual;
                $totals['residual_currency'] += $line->amount_residual_currency;
            }
        }

        return $totals;
    }

    public function recomputeLine(MoveLine $line): MoveLine
    {
        if (! in_array($line->display_type, [DisplayType::PRODUCT, DisplayType::COGS])) {
            $line->price_total = 0.0;

            $line->price_subtotal = 0.0;

            return $line;
        }

        $baseLine = TaxFacade::withTaxDetails($this->productBaseLine($line), $line->company);

        $line->price_subtotal = $baseLine['tax_details']['raw_total_excluded_currency'];

        $line->price_total = $baseLine['tax_details']['raw_total_included_currency'];

        $line->computeBalance();

        $line->computeCreditAndDebit();

        $line->computeAmountCurrency();

        $line->computeAmountResidual();

        return $line;
    }

    public function productBaseLine(MoveLine $line): array
    {
        $isInvoice = $line->move->isInvoice(true);

        $rate = $isInvoice
            ? $line->move->invoice_currency_rate
            : (abs($line->balance) ? abs($line->amount_currency) / abs($line->balance) : 0.0);

        return TaxFacade::makeBaseLine(
            $line,
            priceUnit: $isInvoice ? $line->price_unit : $line->amount_currency,
            quantity: $isInvoice ? $line->quantity : 1.0,
            discount: $isInvoice ? $line->discount : 0.0,
            rate: $rate,
            sign: $isInvoice ? $line->move->direction_sign : 1,
            specialMode: $isInvoice ? false : 'total_excluded',
            isRefund: in_array($line->move->move_type, [MoveType::OUT_REFUND, MoveType::IN_REFUND], true),
        );
    }

    public function roundedBaseAndTaxLines(AccountMove $move, bool $roundFromTaxLines = true): array
    {
        $baseLines = $move->lines
            ->where('display_type', DisplayType::PRODUCT)
            ->map(fn (MoveLine $line) => $this->productBaseLine($line))
            ->all();

        $roundingLines = $move->lines
            ->where('display_type', DisplayType::ROUNDING)
            ->whereNull('tax_repartition_line_id')
            ->map(fn (MoveLine $line) => TaxFacade::makeBaseLine(
                $line,
                priceUnit: $move->direction_sign * $line->amount_currency,
                quantity: 1.0,
                sign: $move->direction_sign,
                specialMode: 'total_excluded',
                specialType: 'cash_rounding',
                isRefund: in_array($move->move_type, [MoveType::OUT_REFUND, MoveType::IN_REFUND]),
                rate: $move->invoice_currency_rate,
            ))
            ->all();

        $baseLines = TaxFacade::addTaxDetailsToAll(
            array_merge($baseLines, $roundingLines),
            $move->company
        );

        $taxLines = $move->lines
            ->whereNotNull('tax_repartition_line_id')
            ->where('display_type', DisplayType::TAX)
            ->map(fn (MoveLine $line) => TaxFacade::makeTaxLine($line, sign: $move->direction_sign))
            ->all();

        $baseLines = TaxFacade::roundTaxDetails(
            $baseLines,
            $move->company,
            $roundFromTaxLines ? $taxLines : []
        );

        return [$baseLines, $taxLines];
    }
}
