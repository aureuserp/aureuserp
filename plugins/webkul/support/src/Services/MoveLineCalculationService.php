<?php

namespace Webkul\Support\Services;

use Webkul\Account\Models\Tax;

class MoveLineCalculationService
{
    /**
     * Calculate all line totals for an invoice line
     *
     * @param array $lineData Current line data
     * @return array Updated line data with calculated values
     */
    public function calculateLineTotals(array $lineData): array
    {
        if (empty($lineData['product_id'])) {
            return $this->resetLineValues($lineData);
        }

        $priceUnit = floatval($lineData['price_unit'] ?? 0);
        $quantity = floatval($lineData['quantity'] ?? 1);
        $taxIds = $lineData['taxes'] ?? [];
        $discountValue = floatval($lineData['discount'] ?? 0);

        $subTotal = $priceUnit * $quantity;

        if ($discountValue > 0) {
            $discountAmount = $subTotal * ($discountValue / 100);
            $subTotal = $subTotal - $discountAmount;
        }

        $taxAmount = 0;
        $taxCalculationResult = $this->calculateTaxes($taxIds, $subTotal, $quantity, $priceUnit);

        if (! empty($taxCalculationResult)) {
            $taxAmount = $taxCalculationResult['taxAmount'];

            if (isset($taxCalculationResult['updatedPriceUnit'])) {
                $priceUnit = $taxCalculationResult['updatedPriceUnit'];
                $subTotal = $priceUnit * $quantity;

                if ($discountValue > 0) {
                    $discountAmount = $subTotal * ($discountValue / 100);
                    $subTotal = $subTotal - $discountAmount;
                }
            }
        }

        $lineData['price_subtotal'] = round($subTotal, 4);
        $lineData['price_tax'] = $taxAmount;
        $lineData['price_total'] = $subTotal + $taxAmount;

        return $lineData;
    }

    /**
     * Reset all line values to zero
     *
     * @param array $lineData Current line data
     * @return array Line data with reset values
     */
    private function resetLineValues(array $lineData): array
    {
        $lineData['price_unit']     = 0;
        $lineData['discount']       = 0;
        $lineData['price_tax']      = 0;
        $lineData['price_subtotal'] = 0;
        $lineData['price_total']    = 0;

        return $lineData;
    }

    /**
     * Calculate taxes for an invoice line
     *
     * @param array $taxIds IDs of selected taxes
     * @param float $baseAmount Base amount for tax calculation
     * @param float $quantity Line quantity
     * @param float $priceUnit Unit price
     * @return array Tax calculation results
     */
    private function calculateTaxes(array $taxIds, float $baseAmount, float $quantity, float $priceUnit): array
    {
        if (empty($taxIds)) {
            return [];
        }

        $taxes = Tax::whereIn('id', $taxIds)
            ->orderBy('sort')
            ->get();

        $taxAmount = 0;
        $taxesComputed = [];
        $updatedPriceUnit = $priceUnit;
        $originalBaseAmount = $baseAmount;

        foreach ($taxes as $tax) {
            $amount = floatval($tax->amount);
            $currentTaxBase = $baseAmount;

            $tax->price_include_override ??= 'tax_excluded';

            if ($tax->is_base_affected) {
                foreach ($taxesComputed as $prevTax) {
                    if ($prevTax['include_base_amount']) {
                        $currentTaxBase += $prevTax['tax_amount'];
                    }
                }
            }

            $currentTaxAmount = 0;

            if ($tax->price_include_override == 'tax_included') {
                $taxFactor = ($tax->amount_type == 'percent') ? $amount / 100 : $amount;
                $currentTaxAmount = $currentTaxBase - ($currentTaxBase / (1 + $taxFactor));

                if (empty($taxesComputed)) {
                    $updatedPriceUnit = $priceUnit - ($currentTaxAmount / $quantity);
                    $baseAmount = $updatedPriceUnit * $quantity;
                    $originalBaseAmount = $baseAmount;
                }
            } elseif ($tax->price_include_override == 'tax_excluded') {
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

            $taxAmount += $currentTaxAmount;
        }

        return [
            'taxAmount'        => $taxAmount,
            'taxesComputed'    => $taxesComputed,
            'updatedPriceUnit' => $updatedPriceUnit,
            'baseAmount'       => $originalBaseAmount,
        ];
    }
}
