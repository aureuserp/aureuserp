<?php

namespace Webkul\Account;

use Webkul\Account\Models\Tax;

class TaxManager
{
    /**
     * Calculate taxes.
     *
     * @param  array  $taxIds
     * @param  float  $subTotal
     * @param  float  $quantity
     * @return array
     */
    public static function collect($taxIds, $subTotal, $quantity)
    {
        if (empty($taxIds)) {
            return [$subTotal, 0, []];
        }

        $taxes = Tax::whereIn('id', $taxIds)
            ->orderBy('sort')
            ->get();

        $taxesComputed = [];

        $totalTaxAmount = 0;

        $adjustedSubTotal = $subTotal;

        foreach ($taxes as $tax) {
            $amount = floatval($tax->amount);

            $tax->price_include_override ??= 'tax_excluded';

            $currentTaxBase = $adjustedSubTotal;

            if ($tax->is_base_affected) {
                foreach ($taxesComputed as $prevTax) {
                    if ($prevTax['include_base_amount']) {
                        $currentTaxBase += $prevTax['tax_amount'];
                    }
                }
            }

            $currentTaxAmount = 0;

            if ($tax->price_include_override == 'tax_included') {
                if ($tax->amount_type == 'percent') {
                    $taxFactor = $amount / 100;

                    $currentTaxAmount = $currentTaxBase - ($currentTaxBase / (1 + $taxFactor));
                } else {
                    $currentTaxAmount = $amount * $quantity;

                    if ($currentTaxAmount > $adjustedSubTotal) {
                        $currentTaxAmount = $adjustedSubTotal;
                    }
                }

                $adjustedSubTotal -= $currentTaxAmount;
            } else {
                if ($tax->amount_type == 'percent') {
                    $currentTaxAmount = $currentTaxBase * $amount / 100;
                } else {
                    $currentTaxAmount = $amount * $quantity;
                }
            }

            $taxesComputed[] = [
                'tax_id'              => $tax->id,
                'tax_amount'          => $currentTaxAmount,
                'include_base_amount' => $tax->include_base_amount,
            ];

            $totalTaxAmount += $currentTaxAmount;
        }

        return [
            round($adjustedSubTotal, 4),
            round($totalTaxAmount, 4),
            $taxesComputed,
        ];
    }
}
