<?php

namespace Webkul\Account;

use Webkul\Account\Models\Tax;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Country;
use Webkul\Support\Models\Currency;
use Webkul\Partner\Models\Partner;
use Illuminate\Database\Eloquent\Model;

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


    public function prepareBaseLineForTaxesComputation(mixed $record, ...$args)
    {
        $getValue = function (string $field, mixed $fallback) use ($record, $args) {
            return $this->getBaseLineFieldValueFromRecord($record, $field, $args, $fallback);
        };

        $currency = $getValue('currency', null)
            ?: $getValue('companyCurrency', null)
            ?: $getValue('company', Company::first())?->currency
            ?: Currency::first()?->currency;

        return array_merge($args, [
            'record' => $record,
            'id' => $getValue('id', 0),

            'product' => $getValue('product', null),
            'taxes' => $getValue('taxes', null),
            'price_unit' => $getValue('price_unit', 0.0),
            'quantity' => $getValue('quantity', 0.0),
            'discount' => $getValue('discount', 0.0),
            'currency' => $currency,

            'special_mode' => $args['special_mode'] ?? false,
            'special_type' => $args['special_type'] ?? false,

            'rate' => $getValue('rate', 1.0),

            'manual_tax_amounts' => $args['manual_tax_amounts'] ?? null,

            'sign' => $getValue('sign', 1.0),
            'is_refund' => $getValue('is_refund', false),
            'tax_tag_invert' => $getValue('tax_tag_invert', false),

            'partner' => $getValue('partner', null),
            'account_id' => $getValue('account_id', null),
            'analytic_distribution' => $getValue('analytic_distribution', null),
        ]);
    }

    public function getBaseLineFieldValueFromRecord(mixed $record, string $field, array $extraValues, mixed $fallback = null)
    {
        $needOrigin = $fallback instanceof Model;

        if (array_key_exists($field, $extraValues)) {
            $value = !empty($extraValues[$field]) ? $extraValues[$field] : $fallback;
        } elseif ($record instanceof Model && array_key_exists($field, $record->getAttributes())) {
            $value = $record->getAttribute($field);
        } elseif ($record instanceof Model && method_exists($record, $field)) {
            $value = $record->$field;
        } elseif (is_array($record)) {
            $value = array_key_exists($field, $record) ? $record[$field] : $fallback;
        } else {
            $value = $fallback;
        }

        if ($needOrigin && isset($value->_origin)) {
            $value = $value->_origin;
        }

        return $value;
    }

    public function addTaxDetailsInBaseLine(array $baseLine, Company $company, $roundingMethod = null)
    {
        $priceUnitAfterDiscount = $baseLine['price_unit'] * (1 - ($baseLine['discount'] / 100));

        $taxesComputation = $this->getTaxDetails(
            taxes : $baseLine['taxes'],
            priceUnit : $priceUnitAfterDiscount,
            quantity : $baseLine['quantity'],
            precisionRounding : $baseLine['currency']->rounding ?? 2,
            roundingMethod : $roundingMethod ?? $company->tax_calculation_rounding_method,
            product : $baseLine['product'],
            specialMode : $baseLine['special_mode'],
            manualTaxAmounts : $baseLine['manual_tax_amounts'],
        );

        $rate = $baseLine['rate'] ?: 1.0;

        $taxDetails = [
            'raw_total_excluded_currency' => $taxesComputation['total_excluded'],
            'raw_total_excluded'          => $rate ? $taxesComputation['total_excluded'] / $rate : 0.0,
            'raw_total_included_currency' => $taxesComputation['total_included'],
            'raw_total_included'          => $rate ? $taxesComputation['total_included'] / $rate : 0.0,
            'taxes_data'                  => [],
        ];

        if ($company->tax_calculation_rounding_method === 'round_per_line') {
            $taxDetails['raw_total_excluded'] = $company->currency->round($taxDetails['raw_total_excluded']);
            $taxDetails['raw_total_included'] = $company->currency->round($taxDetails['raw_total_included']);
        }

        foreach ($taxesComputation['taxes_data'] as $taxData) {
            $taxAmount  = $rate ? $taxData['tax_amount'] / $rate : 0.0;
            $baseAmount = $rate ? $taxData['base_amount'] / $rate : 0.0;

            if ($company->tax_calculation_rounding_method === 'round_per_line') {
                $taxAmount  = $company->currency->round($taxAmount);
                $baseAmount = $company->currency->round($baseAmount);
            }

            $taxDetails['taxes_data'][] = array_merge($taxData, [
                'raw_tax_amount_currency' => $taxData['tax_amount'],
                'raw_tax_amount'          => $taxAmount,
                'raw_base_amount_currency'=> $taxData['base_amount'],
                'raw_base_amount'         => $baseAmount,
            ]);
        }

        $baseLine['tax_details'] = $taxDetails;

        return $baseLine;
    }

    public function getTaxDetails(
        mixed $taxes,
        float $priceUnit,
        float $quantity,
        float $precisionRounding = 0.01,
        $roundingMethod = 'round_per_line',
        mixed $product = null,
        bool $specialMode = false,
        mixed $manualTaxAmounts = null,
    )
    {
        $batchingResults = $this->batchForTaxesComputation($taxes, $specialMode);

        $sortedTaxes = $batchingResults['sorted_taxes'];

        $taxesData = [];

        $reverseChargeTaxesData = [];

        $addTaxAmountToResults = function ($tax, $taxAmount) use ($sortedTaxes, $taxesData, $roundingMethod, $specialMode) {
            $taxesData[$tax->id]['tax_amount'] = $taxAmount;

            if ($roundingMethod == 'round_per_line') {
            }

            if ($tax->has_negative_factor) {
                $this->propagateExtraTaxesBase($sortedTaxes, $tax, $taxesData, $specialMode);
            }
        };

        // $addTaxAmountToResults = function
    }

    protected function batchForTaxesComputation(mixed $taxes, string|bool $specialMode = false): array
    {
        
        $results = [
            'batch_per_tax' => [],
            'group_per_tax' => [],
            'sorted_taxes' => collect(),
        ];

        $taxes = $taxes->orderBy('sequence')->get();

        foreach ($taxes as $tax) {
            if ($tax->amount_type === 'group') {
                $children = $tax->childrenTaxes()->orderBy('sequence')->get();
                $results['sorted_taxes'] = $results['sorted_taxes']->merge($children);
                foreach ($children as $child) {
                    $results['group_per_tax'][$child->id] = $tax;
                }
            } else {
                $results['sorted_taxes']->push($tax);
            }
        }

        foreach ($results['sorted_taxes'] as $tax) {
            $results['batch_per_tax'][$tax->id] = [$tax];
        }

        return $results;
    }

    public static function propagateExtraTaxesBase($taxes, $tax, array &$taxesData, string|bool $specialMode = false): void
    {
        $getTaxBefore = function () use ($taxes, $tax, &$taxesData) {
            foreach ($taxes as $taxBefore) {
                if (collect($taxesData[$tax->id]['batch'])->pluck('id')->contains($taxBefore->id)) {
                    break;
                }

                yield $taxBefore;
            }
        };

        $getTaxAfter = function () use ($taxes, $tax, &$taxesData) {
            foreach ($taxes->reverse() as $taxAfter) {
                if (collect($taxesData[$tax->id]['batch'])->pluck('id')->contains($taxAfter->id)) {
                    break;
                }

                yield $taxAfter;
            }
        };

        $addExtraBase = function ($otherTax, int $sign) use (&$taxesData, $tax) {
            if (!isset($taxesData[$tax->id]['tax_amount'])) {
                return;
            }

            $taxAmount = $taxesData[$tax->id]['tax_amount'];

            if (!isset($taxesData[$otherTax->id]['tax_amount'])) {
                $taxesData[$otherTax->id]['extra_base_for_tax'] += $sign * $taxAmount;
            }

            $taxesData[$otherTax->id]['extra_base_for_base'] += $sign * $taxAmount;
        };

        if ($tax->price_include) {
            if ($specialMode === false || $specialMode === 'total_included') {
                if (!$tax->include_base_amount) {
                    foreach ($getTaxAfter() as $otherTax) {
                        if ($otherTax->price_include) {
                            $addExtraBase($otherTax, -1);
                        }
                    }
                }

                foreach ($getTaxBefore() as $otherTax) {
                    $addExtraBase($otherTax, -1);
                }
            } else {
                foreach ($getTaxAfter() as $otherTax) {
                    if (!$otherTax->price_include || $tax->include_base_amount) {
                        $addExtraBase($otherTax, 1);
                    }
                }
            }
        } else {
            if ($specialMode === false || $specialMode === 'total_excluded') {
                if ($tax->include_base_amount) {
                    foreach ($getTaxAfter() as $otherTax) {
                        if ($otherTax->is_base_affected) {
                            $addExtraBase($otherTax, 1);
                        }
                    }
                }
            } else {
                if (!$tax->include_base_amount) {
                    foreach ($getTaxAfter() as $otherTax) {
                        $addExtraBase($otherTax, -1);
                    }
                }

                foreach ($getTaxBefore() as $otherTax) {
                    $addExtraBase($otherTax, -1);
                }
            }
        }
    }
}
