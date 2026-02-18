<?php

namespace Webkul\Account;

use Illuminate\Database\Eloquent\Model;
use Webkul\Account\Enums\AmountType;
use Webkul\Account\Enums\TaxIncludeOverride;
use Webkul\Account\Models\Account;
use Webkul\Account\Models\Product;
use Webkul\Account\Models\Tax;
use Illuminate\Support\Facades\Auth;
use Webkul\Account\Models\TaxPartition;
use Webkul\Account\Settings\TaxesSettings;
use Webkul\Partner\Models\Partner;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Currency;

class TaxManager
{
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

            if (! $tax->price_include_override) {
                $tax->price_include_override = app(TaxesSettings::class)->account_price_include ?? TaxIncludeOverride::TAX_INCLUDED;
            }

            $currentTaxBase = $adjustedSubTotal;

            if ($tax->is_base_affected) {
                foreach ($taxesComputed as $prevTax) {
                    if ($prevTax['include_base_amount']) {
                        $currentTaxBase += $prevTax['tax_amount'];
                    }
                }
            }

            $currentTaxAmount = 0;

            if ($tax->price_include_override == TaxIncludeOverride::TAX_INCLUDED) {
                if ($tax->amount_type == AmountType::PERCENT) {
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
                if ($tax->amount_type == AmountType::PERCENT) {
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
            ?: new Currency;

        return array_merge($args, [
            'record' => $record,
            'id'     => $getValue('id', 0),

            'product'    => $getValue('product', new Product),
            'taxes'      => $getValue('taxes', new Tax),
            'price_unit' => $getValue('price_unit', 0.0),
            'quantity'   => $getValue('quantity', 0.0),
            'discount'   => $getValue('discount', 0.0),
            'currency'   => $currency,

            'special_mode' => $args['special_mode'] ?? false,
            'special_type' => $args['special_type'] ?? false,

            'rate' => $getValue('rate', 1.0),

            'manual_tax_amounts' => $args['manual_tax_amounts'] ?? null,

            'sign'           => $getValue('sign', 1.0),
            'is_refund'      => $getValue('is_refund', false),
            'tax_tag_invert' => $getValue('tax_tag_invert', false),

            'partner'               => $getValue('partner', new Partner),
            'account'               => $getValue('account', new Account),
            'analytic_distribution' => $getValue('analytic_distribution', null),
        ]);
    }

    public function prepareTaxLineForTaxesComputation(mixed $record, ...$args)
    {
        $getValue = function (string $field, mixed $fallback) use ($record, $args) {
            return $this->getBaseLineFieldValueFromRecord($record, $field, $args, $fallback);
        };

        $currency = $getValue('currency', null)
            ?: $getValue('companyCurrency', null)
            ?: $getValue('company', Company::first())?->currency
            ?: new Currency;

        return array_merge($args, [
            'record' => $record,
            'id'     => $getValue('id', 0),

            'taxRepartitionLine'    => $getValue('taxRepartitionLine', new TaxPartition),
            'groupTax'              => $getValue('groupTax', new Tax),
            'taxes'                 => $getValue('taxes', new Tax),
            'tax_tags'              => $getValue('tax_tags', []),
            'currency'              => $currency,
            'partner'               => $getValue('partner', new Partner),
            'account'               => $getValue('account', new Account),
            'analytic_distribution' => $getValue('analytic_distribution', null),
            'sign'                  => $getValue('sign', 1.0),
            'amount_currency'       => $getValue('amount_currency', 0),
            'balance'               => $getValue('balance', 0),
        ]);
    }

    public function roundBaseLinesTaxDetails($baseLines, $company, $taxLines = [])
    {
        $totalPerTax = [];
        $totalPerBase = [];
        $mapTotalPerTaxKeyForTaxLineKey = [];

        foreach ($baseLines as $baseLineIndex => $baseLine) {
            $currency = $baseLine['currency'];
            $taxDetails = $baseLine['tax_details'];
            $taxDetails['total_excluded_currency'] = $currency->round($taxDetails['raw_total_excluded_currency']);
            $taxDetails['total_excluded'] = $company->currency->round($taxDetails['raw_total_excluded']);
            $taxDetails['delta_total_excluded_currency'] = 0.0;
            $taxDetails['delta_total_excluded'] = 0.0;
            $taxDetails['total_included_currency'] = $currency->round($taxDetails['raw_total_included_currency']);
            $taxDetails['total_included'] = $company->currency->round($taxDetails['raw_total_included']);
            $taxesData = $taxDetails['taxes_data'];

            foreach ($taxesData as $index => $taxData) {
                $tax = $taxData['tax'];

                $taxData['tax_amount_currency'] = $currency->round($taxData['raw_tax_amount_currency']);
                $taxData['tax_amount'] = $company->currency->round($taxData['raw_tax_amount']);
                $taxData['base_amount_currency'] = $currency->round($taxData['raw_base_amount_currency']);
                $taxData['base_amount'] = $company->currency->round($taxData['raw_base_amount']);

                $taxRoundingKey = json_encode([$tax->id ?? null, $currency->id, $baseLine['is_refund'], $taxData['is_reverse_charge']]);
                $taxLineKey = json_encode([$tax->id ?? null, $currency->id, $baseLine['is_refund']]);

                if (! isset($mapTotalPerTaxKeyForTaxLineKey[$taxLineKey])) {
                    $mapTotalPerTaxKeyForTaxLineKey[$taxLineKey] = [];
                }

                $mapTotalPerTaxKeyForTaxLineKey[$taxLineKey][$taxRoundingKey] = true;

                if (! isset($totalPerTax[$taxRoundingKey])) {
                    $totalPerTax[$taxRoundingKey] = [
                        'base_amount_currency'     => 0.0,
                        'base_amount'              => 0.0,
                        'raw_base_amount_currency' => 0.0,
                        'raw_base_amount'          => 0.0,
                        'tax_amount_currency'      => 0.0,
                        'tax_amount'               => 0.0,
                        'raw_tax_amount_currency'  => 0.0,
                        'raw_tax_amount'           => 0.0,
                        'base_lines'               => [],
                    ];
                }

                $totalPerTax[$taxRoundingKey]['tax_amount_currency'] += $taxData['tax_amount_currency'];
                $totalPerTax[$taxRoundingKey]['raw_tax_amount_currency'] += $taxData['raw_tax_amount_currency'];
                $totalPerTax[$taxRoundingKey]['tax_amount'] += $taxData['tax_amount'];
                $totalPerTax[$taxRoundingKey]['raw_tax_amount'] += $taxData['raw_tax_amount'];
                $totalPerTax[$taxRoundingKey]['base_amount_currency'] += $taxData['base_amount_currency'];
                $totalPerTax[$taxRoundingKey]['raw_base_amount_currency'] += $taxData['raw_base_amount_currency'];
                $totalPerTax[$taxRoundingKey]['base_amount'] += $taxData['base_amount'];
                $totalPerTax[$taxRoundingKey]['raw_base_amount'] += $taxData['raw_base_amount'];

                if (! $baseLine['special_type']) {
                    $totalPerTax[$taxRoundingKey]['base_lines'][] = $baseLineIndex;
                }

                if ($index === 0) {
                    $baseRoundingKey = json_encode([$currency->id, $baseLine['is_refund']]);

                    if (! isset($totalPerBase[$baseRoundingKey])) {
                        $totalPerBase[$baseRoundingKey] = [
                            'base_amount_currency'     => 0.0,
                            'base_amount'              => 0.0,
                            'raw_base_amount_currency' => 0.0,
                            'raw_base_amount'          => 0.0,
                            'base_lines'               => [],
                        ];
                    }

                    $totalPerBase[$baseRoundingKey]['base_amount_currency'] += $taxData['base_amount_currency'];
                    $totalPerBase[$baseRoundingKey]['raw_base_amount_currency'] += $taxData['raw_base_amount_currency'];
                    $totalPerBase[$baseRoundingKey]['base_amount'] += $taxData['base_amount'];
                    $totalPerBase[$baseRoundingKey]['raw_base_amount'] += $taxData['raw_base_amount'];

                    if (! $baseLine['special_type']) {
                        $totalPerBase[$baseRoundingKey]['base_lines'][] = $baseLineIndex;
                    }
                }

                $taxesData[$index] = $taxData;
            }

            if (empty($taxesData)) {
                $taxRoundingKey = json_encode([null, $currency->id, $baseLine['is_refund'], false]);

                if (! isset($totalPerTax[$taxRoundingKey])) {
                    $totalPerTax[$taxRoundingKey] = [
                        'base_amount_currency'     => 0.0,
                        'base_amount'              => 0.0,
                        'raw_base_amount_currency' => 0.0,
                        'raw_base_amount'          => 0.0,
                        'tax_amount_currency'      => 0.0,
                        'tax_amount'               => 0.0,
                        'raw_tax_amount_currency'  => 0.0,
                        'raw_tax_amount'           => 0.0,
                        'base_lines'               => [],
                    ];
                }

                $totalPerTax[$taxRoundingKey]['base_amount_currency'] += $taxDetails['total_excluded_currency'];
                $totalPerTax[$taxRoundingKey]['raw_base_amount_currency'] += $taxDetails['raw_total_excluded_currency'];
                $totalPerTax[$taxRoundingKey]['base_amount'] += $taxDetails['total_excluded'];
                $totalPerTax[$taxRoundingKey]['raw_base_amount'] += $taxDetails['raw_total_excluded'];

                if (! $baseLine['special_type']) {
                    $totalPerTax[$taxRoundingKey]['base_lines'][] = $baseLineIndex;
                }

                $baseRoundingKey = json_encode([$currency->id, $baseLine['is_refund']]);

                if (! isset($totalPerBase[$baseRoundingKey])) {
                    $totalPerBase[$baseRoundingKey] = [
                        'base_amount_currency'     => 0.0,
                        'base_amount'              => 0.0,
                        'raw_base_amount_currency' => 0.0,
                        'raw_base_amount'          => 0.0,
                        'base_lines'               => [],
                    ];
                }

                $totalPerBase[$baseRoundingKey]['base_amount_currency'] += $taxDetails['total_excluded_currency'];
                $totalPerBase[$baseRoundingKey]['raw_base_amount_currency'] += $taxDetails['raw_total_excluded_currency'];
                $totalPerBase[$baseRoundingKey]['base_amount'] += $taxDetails['total_excluded'];
                $totalPerBase[$baseRoundingKey]['raw_base_amount'] += $taxDetails['raw_total_excluded'];

                if (! $baseLine['special_type']) {
                    $totalPerBase[$baseRoundingKey]['base_lines'][] = $baseLineIndex;
                }
            }

            $taxDetails['taxes_data'] = $taxesData;
            $baseLine['tax_details'] = $taxDetails;
            $baseLines[$baseLineIndex] = $baseLine;
        }

        foreach ($totalPerTax as $key => $taxAmounts) {
            $decoded = json_decode($key, true);

            $currency = Currency::find($decoded[1]);

            $taxAmounts['raw_tax_amount_currency'] = $currency->round($taxAmounts['raw_tax_amount_currency']);
            $taxAmounts['raw_tax_amount'] = $company->currency->round($taxAmounts['raw_tax_amount']);
            $taxAmounts['raw_base_amount_currency'] = $currency->round($taxAmounts['raw_base_amount_currency']);
            $taxAmounts['raw_base_amount'] = $company->currency->round($taxAmounts['raw_base_amount']);

            $totalPerTax[$key] = $taxAmounts;
        }

        foreach ($totalPerBase as $key => $baseAmounts) {
            $decoded = json_decode($key, true);

            $currency = Currency::find($decoded[0]);

            $baseAmounts['raw_base_amount_currency'] = $currency->round($baseAmounts['raw_base_amount_currency']);
            $baseAmounts['raw_base_amount'] = $company->currency->round($baseAmounts['raw_base_amount']);

            $totalPerBase[$key] = $baseAmounts;
        }

        if (! empty($taxLines)) {
            $totalPerTaxLineKey = [];

            foreach ($taxLines as $taxLine) {
                $taxRepartitionLine = $taxLine['taxRepartitionLine'];

                $taxLineKey = json_encode([$taxRepartitionLine->tax_id, $taxLine['currency']->id, $taxRepartitionLine->document_type === 'refund']);

                if (! isset($totalPerTaxLineKey[$taxLineKey])) {
                    $totalPerTaxLineKey[$taxLineKey] = [
                        'tax_amount_currency' => 0.0,
                        'tax_amount'          => 0.0,
                    ];
                }

                $totalPerTaxLineKey[$taxLineKey]['tax_amount_currency'] += $taxLine['sign'] * $taxLine['amount_currency'];
                $totalPerTaxLineKey[$taxLineKey]['tax_amount'] += $taxLine['sign'] * $taxLine['balance'];
            }

            foreach ($totalPerTaxLineKey as $taxLineKey => $taxLineAmounts) {
                $roundingKeys = array_keys($mapTotalPerTaxKeyForTaxLineKey[$taxLineKey] ?? []);

                if (empty($roundingKeys)) {
                    continue;
                }

                $rawTaxAmountCurrency = 0.0;
                $rawTaxAmount = 0.0;

                foreach ($roundingKeys as $taxRoundingKey) {
                    $rawTaxAmountCurrency += $totalPerTax[$taxRoundingKey]['raw_tax_amount_currency'];
                    $rawTaxAmount += $totalPerTax[$taxRoundingKey]['raw_tax_amount'];
                }

                $deltaRawTaxAmountCurrency = $taxLineAmounts['tax_amount_currency'] - $rawTaxAmountCurrency;
                $deltaRawTaxAmount = $taxLineAmounts['tax_amount'] - $rawTaxAmount;

                $biggestKey = null;
                $biggestAmount = null;

                foreach ($roundingKeys as $roundingKey) {
                    if ($biggestAmount === null || $totalPerTax[$roundingKey]['raw_tax_amount_currency'] > $biggestAmount) {
                        $biggestAmount = $totalPerTax[$roundingKey]['raw_tax_amount_currency'];
                        $biggestKey = $roundingKey;
                    }
                }

                if ($biggestKey !== null) {
                    $totalPerTax[$biggestKey]['raw_tax_amount_currency'] += $deltaRawTaxAmountCurrency;
                    $totalPerTax[$biggestKey]['raw_tax_amount'] += $deltaRawTaxAmount;
                }
            }
        }

        foreach ($totalPerTax as $key => $taxAmounts) {
            if (empty($taxAmounts['base_lines'])) {
                continue;
            }

            $maxBaseLineIndex = null;
            $maxAmount = null;

            foreach ($taxAmounts['base_lines'] as $blIndex) {
                if ($maxAmount === null || $baseLines[$blIndex]['tax_details']['total_included_currency'] > $maxAmount) {
                    $maxAmount = $baseLines[$blIndex]['tax_details']['total_included_currency'];
                    $maxBaseLineIndex = $blIndex;
                }
            }

            $totalPerTax[$key]['reference_base_line_index'] = $maxBaseLineIndex;

            $decoded = json_decode($key, true);
            $taxId = $decoded[0];

            if ($taxId === null) {
                continue;
            }

            $isReverseCharge = $decoded[3];
            $deltaTaxAmountCurrency = $taxAmounts['raw_tax_amount_currency'] - $taxAmounts['tax_amount_currency'];
            $deltaTaxAmount = $taxAmounts['raw_tax_amount'] - $taxAmounts['tax_amount'];

            $foundTaxDataIndex = null;

            foreach ($baseLines[$maxBaseLineIndex]['tax_details']['taxes_data'] as $tdIndex => $td) {
                if ($td['tax']->id === $taxId && $td['is_reverse_charge'] === $isReverseCharge) {
                    $foundTaxDataIndex = $tdIndex;

                    break;
                }
            }

            if ($foundTaxDataIndex !== null) {
                $totalPerTax[$key]['reference_tax_data_index'] = $foundTaxDataIndex;
                $baseLines[$maxBaseLineIndex]['tax_details']['taxes_data'][$foundTaxDataIndex]['tax_amount_currency'] += $deltaTaxAmountCurrency;
                $baseLines[$maxBaseLineIndex]['tax_details']['taxes_data'][$foundTaxDataIndex]['tax_amount'] += $deltaTaxAmount;
            }
        }

        foreach ($totalPerTax as $key => $taxAmounts) {
            if (! isset($taxAmounts['reference_base_line_index'])) {
                continue;
            }

            $baseLineIndex = $taxAmounts['reference_base_line_index'];
            $decoded = json_decode($key, true);
            $currency = Currency::find($decoded[1]);

            $deltaBaseAmountCurrency = $taxAmounts['raw_base_amount_currency'] - $taxAmounts['base_amount_currency'];
            $deltaBaseAmount = $taxAmounts['raw_base_amount'] - $taxAmounts['base_amount'];

            if ($currency->isZero($deltaBaseAmountCurrency) && $company->currency->isZero($deltaBaseAmount)) {
                continue;
            }

            if (isset($taxAmounts['reference_tax_data_index'])) {
                $taxDataIndex = $taxAmounts['reference_tax_data_index'];
                $baseLines[$baseLineIndex]['tax_details']['taxes_data'][$taxDataIndex]['base_amount_currency'] += $deltaBaseAmountCurrency;
                $baseLines[$baseLineIndex]['tax_details']['taxes_data'][$taxDataIndex]['base_amount'] += $deltaBaseAmount;
            } else {
                $baseLines[$baseLineIndex]['tax_details']['delta_total_excluded_currency'] += $deltaBaseAmountCurrency;
                $baseLines[$baseLineIndex]['tax_details']['delta_total_excluded'] += $deltaBaseAmount;

                $baseRoundingKey = json_encode([$currency->id, $baseLines[$baseLineIndex]['is_refund']]);

                if (isset($totalPerBase[$baseRoundingKey])) {
                    $totalPerBase[$baseRoundingKey]['base_amount_currency'] += $deltaBaseAmountCurrency;
                    $totalPerBase[$baseRoundingKey]['base_amount'] += $deltaBaseAmount;
                }
            }
        }

        foreach ($totalPerBase as $key => $baseAmounts) {
            if (empty($baseAmounts['base_lines'])) {
                continue;
            }

            $maxBaseLineIndex = null;
            $maxAmount = null;

            foreach ($baseAmounts['base_lines'] as $blIndex) {
                if ($maxAmount === null || $baseLines[$blIndex]['tax_details']['total_included_currency'] > $maxAmount) {
                    $maxAmount = $baseLines[$blIndex]['tax_details']['total_included_currency'];
                    $maxBaseLineIndex = $blIndex;
                }
            }

            $decoded = json_decode($key, true);
            $currency = Currency::find($decoded[0]);

            $deltaBaseAmountCurrency = $baseAmounts['raw_base_amount_currency'] - $baseAmounts['base_amount_currency'];
            $deltaBaseAmount = $baseAmounts['raw_base_amount'] - $baseAmounts['base_amount'];

            if ($currency->isZero($deltaBaseAmountCurrency) && $company->currency->isZero($deltaBaseAmount)) {
                continue;
            }

            $baseLines[$maxBaseLineIndex]['tax_details']['delta_total_excluded_currency'] += $deltaBaseAmountCurrency;
            $baseLines[$maxBaseLineIndex]['tax_details']['delta_total_excluded'] += $deltaBaseAmount;
        }

        return $baseLines;
    }

    public function getBaseLineFieldValueFromRecord(mixed $record, string $field, array $extraValues, mixed $fallback = null)
    {
        $needOrigin = $fallback instanceof Model;

        if (array_key_exists($field, $extraValues)) {
            $value = ! empty($extraValues[$field]) ? $extraValues[$field] : $fallback;
        } elseif ($record instanceof Model && array_key_exists($field, $record->getAttributes())) {
            $value = $record->getAttribute($field) ?? $fallback;
        } elseif ($record instanceof Model && method_exists($record, $field)) {
            $value = $record->$field ?? $fallback;
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

    public function addTaxDetailsInBaseLines(array $baseLines, $company): array
    {
        return array_map(
            fn ($baseLine) => $this->addTaxDetailsInBaseLine($baseLine, $company),
            $baseLines
        );
    }

    public function addAccountingDataInBaseLinesTaxDetails($baseLines, $company)
    {
        return array_map(
            fn ($baseLine) => $this->addAccountingDataToBaseLineTaxDetails($baseLine, $company),
            $baseLines
        );
    }

    public function addTaxDetailsInBaseLine(array $baseLine, $company, $roundingMethod = null)
    {
        $priceUnitAfterDiscount = $baseLine['priceUnit'] * (1 - ($baseLine['discount'] / 100));

        $defaultRoundingMethod = (new TaxesSettings)->tax_calculation_rounding_method;

        $taxesComputation = $this->getTaxDetails(
            taxes : $baseLine['taxes'],
            priceUnit : $priceUnitAfterDiscount,
            quantity : $baseLine['quantity'],
            precisionRounding : $baseLine['currency']->rounding ?? 2,
            roundingMethod : $roundingMethod ?? $defaultRoundingMethod,
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

        if ($defaultRoundingMethod === 'round_per_line') {
            $taxDetails['raw_total_excluded'] = $company->currency->round($taxDetails['raw_total_excluded']);
            $taxDetails['raw_total_included'] = $company->currency->round($taxDetails['raw_total_included']);
        }

        foreach ($taxesComputation['taxes_data'] as $taxData) {
            $taxAmount = $rate ? $taxData['tax_amount'] / $rate : 0.0;
            $baseAmount = $rate ? $taxData['base_amount'] / $rate : 0.0;

            if ($defaultRoundingMethod === 'round_per_line') {
                $taxAmount = $company->currency->round($taxAmount);
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

    public function addAccountingDataToBaseLineTaxDetails($baseLine, $company)
    {
        $companyCurrency = $company->currency;

        $currency = $baseLine['currency'] ?? $companyCurrency;

        $repartitionLinesField = $baseLine['is_refund']
            ? 'refundRepartitionLines'
            : 'invoiceRepartitionLines';

        $taxesData = $baseLine['tax_details']['taxes_data'] ?? [];

        foreach ($taxesData as $taxIndex => $taxData) {
            $tax = $taxData['tax'];

            if ($taxData['is_reverse_charge']) {
                $taxRepartitions = $tax->{$repartitionLinesField}->filter(
                    fn ($x) => $x->repartition_type === 'tax' && $x->factor < 0.0
                );

                $taxRepartitionSign = -1.0;
            } else {
                $taxRepartitions = $tax->{$repartitionLinesField}->filter(
                    fn ($x) => $x->repartition_type === 'tax' && $x->factor >= 0.0
                );

                $taxRepartitionSign = 1.0;
            }

            $totalTaxRepAmounts = [
                'tax_amount_currency' => 0.0,
                'tax_amount'          => 0.0,
            ];

            $taxRepartitionsData = [];

            foreach ($taxRepartitions as $taxRepartition) {
                $taxAmountCurrency = config('compute_all_use_raw_base_lines', false)
                    ? ($taxData['raw_tax_amount_currency'] ?? 0.0)
                    : ($taxData['tax_amount_currency'] ?? 0.0);

                $taxRepartitionData = [
                    'tax_rep'             => $taxRepartition,
                    'tax_amount_currency' => $currency->round($taxAmountCurrency * $taxRepartition->factor * $taxRepartitionSign),
                    'tax_amount'          => $currency->round($taxData['tax_amount'] * $taxRepartition->factor * $taxRepartitionSign),
                    'account'             => $taxRepartition->account ?? $baseLine['account'],
                ];

                $totalTaxRepAmounts['tax_amount_currency'] += $taxRepartitionData['tax_amount_currency'];
                $totalTaxRepAmounts['tax_amount'] += $taxRepartitionData['tax_amount'];

                $taxRepartitionsData[] = $taxRepartitionData;
            }

            usort($taxRepartitionsData, function ($a, $b) {
                $absA = abs($a['tax_amount_currency']);
                $absB = abs($b['tax_amount_currency']);

                if ($absA !== $absB) {
                    return $absB <=> $absA;
                }

                return abs($b['tax_amount']) <=> abs($a['tax_amount']);
            });

            foreach ([
                ['tax_amount_currency', $currency],
                ['tax_amount', $companyCurrency],
            ] as [$field, $fieldCurrency]) {
                $taxAmount = config('compute_all_use_raw_base_lines', false)
                    ? ($taxData["raw_{$field}"] ?? 0.0)
                    : ($taxData[$field] ?? 0.0);

                $totalError = $taxAmount - $totalTaxRepAmounts[$field];
                $numberOfErrors = round(abs($totalError / $fieldCurrency->rounding));

                if ($numberOfErrors == 0 || count($taxRepartitionsData) == 0) {
                    continue;
                }

                $amountToDistribute = $totalError / $numberOfErrors;

                $index = 0;

                while ($numberOfErrors > 0) {
                    $taxRepartitionsData[$index][$field] += $amountToDistribute;

                    $numberOfErrors--;

                    $index = ($index + 1) % count($taxRepartitionsData);
                }
            }

            $taxesData[$taxIndex]['tax_reps_data'] = $taxRepartitionsData;
        }

        $subsequentTaxes = collect();

        foreach (array_reverse($taxesData, true) as $taxIndex => $taxData) {
            $tax = $taxData['tax'];

            foreach ($taxData['tax_reps_data'] as $repIndex => $taxRepartitionData) {
                $taxRepartition = $taxRepartitionData['tax_rep'];

                $taxRepartitionData['taxes'] = collect();

                if ($tax->include_base_amount) {
                    $taxRepartitionData['taxes'] = $taxRepartitionData['taxes']->merge($subsequentTaxes);
                }

                $baseLineGroupingKey = [
                    'partner_id'            => $baseLine['partner']->id,
                    'currency_id'           => $baseLine['currency']->id,
                    'analytic_distribution' => $baseLine['analytic_distribution'],
                    'account_id'            => $baseLine['account']->id,
                    'tax_ids'               => $baseLine instanceof Model
                        ? [$baseLine->id]
                        : $baseLine['taxes']->pluck('id')->toArray(),
                ];

                $taxRepartitionData['grouping_key'] = $this->prepareBaseLineTaxRepartitionGroupingKey(
                    $baseLine,
                    $baseLineGroupingKey,
                    $taxData,
                    $taxRepartitionData
                );

                $taxesData[$taxIndex]['tax_reps_data'][$repIndex] = $taxRepartitionData;
            }

            if ($tax->is_base_affected) {
                $subsequentTaxes = $subsequentTaxes->push($tax);
            }
        }

        $baseLine['tax_details']['taxes_data'] = $taxesData;

        return $baseLine;
    }

    public function prepareTaxLines($baseLines, $company, $taxLines = [])
    {
        $taxLinesMapping = [];

        $baseLinesToUpdate = [];

        foreach ($baseLines as $baseLine) {
            $baseLinesToUpdate[] = array_merge($baseLine, [
                'amount_currency' => $baseLine['sign'] * ($baseLine['tax_details']['total_excluded_currency'] + $baseLine['tax_details']['delta_total_excluded_currency']),
                'balance'         => $baseLine['sign'] * ($baseLine['tax_details']['total_excluded'] + $baseLine['tax_details']['delta_total_excluded']),
            ]);

            foreach ($baseLine['tax_details']['taxes_data'] as $taxData) {
                $tax = $taxData['tax'];

                foreach ($taxData['tax_reps_data'] as $taxRepData) {
                    $groupingKey = json_encode($taxRepData['grouping_key']);

                    if (! isset($taxLinesMapping[$groupingKey])) {
                        $taxLinesMapping[$groupingKey] = [
                            'grouping_key'    => $taxRepData['grouping_key'],
                            'tax_base_amount' => 0.0,
                            'amount_currency' => 0.0,
                            'balance'         => 0.0,
                            'tax_line_id'     => null,
                            'tax_group_id'    => null,
                        ];
                    }

                    $taxLinesMapping[$groupingKey]['name'] = $tax->name;
                    $taxLinesMapping[$groupingKey]['tax_base_amount'] += $baseLine['sign'] * $taxData['base_amount'] * ($baseLine['tax_tag_invert'] ? -1 : 1);
                    $taxLinesMapping[$groupingKey]['amount_currency'] += $baseLine['sign'] * $taxRepData['tax_amount_currency'];
                    $taxLinesMapping[$groupingKey]['balance'] += $baseLine['sign'] * $taxRepData['tax_amount'];
                    $taxLinesMapping[$groupingKey]['tax_line_id'] = $taxRepData['tax_rep']->tax_id;
                    $taxLinesMapping[$groupingKey]['tax_group_id'] = $taxRepData['tax_rep']->tax->tax_group_id;
                }
            }
        }

        $taxLinesMapping = array_filter($taxLinesMapping, function ($v) use ($company) {
            $currencyId = $v['grouping_key']['currency_id'] ?? null;

            if ($currencyId) {
                $currency = Currency::find($currencyId);

                if (! $currency->isZero($v['amount_currency'])) {
                    return true;
                }
            }

            return ! $company->currency_id->isZero($v['balance']);
        });

        $taxLinesToUpdate = [];

        $taxLinesToDelete = [];

        $processedKeys = [];

        foreach ($taxLines as $taxLine) {
            $groupingKey = json_encode([
                'partner_id'              => $taxLine['partner']->id,
                'currency_id'             => $taxLine['currency']->id,
                'analytic_distribution'   => $taxLine['analytic_distribution'],
                'account_id'              => $taxLine['account']->id,
                'tax_ids'                 => $taxLine['taxes']->pluck('id')->toArray(),
                'tax_repartition_line_id' => $taxLine['taxRepartitionLine']->id,
                'group_tax_id'            => $taxLine['groupTax']->id,
            ]);

            if (isset($taxLinesMapping[$groupingKey]) && ! in_array($groupingKey, $processedKeys)) {
                $amounts = $taxLinesMapping[$groupingKey];

                unset($taxLinesMapping[$groupingKey]);

                $taxLinesToUpdate[] = array_merge($taxLine, $amounts['grouping_key'], $amounts);

                $processedKeys[] = $groupingKey;
            } else {
                $taxLinesToDelete[] = $taxLine;
            }
        }

        $taxLinesToAdd = [];

        foreach ($taxLinesMapping as $mapping) {
            $groupingKey = $mapping['grouping_key'];

            unset($mapping['grouping_key']);

            $taxLinesToAdd[] = array_merge($groupingKey, $mapping);
        }

        return [
            'tax_lines_to_add'     => $taxLinesToAdd,
            'tax_lines_to_delete'  => $taxLinesToDelete,
            'tax_lines_to_update'  => $taxLinesToUpdate,
            'base_lines_to_update' => $baseLinesToUpdate,
        ];
    }

    public function prepareBaseLineTaxRepartitionGroupingKey($baseLine, $baseLineGroupingKey, $taxData, $taxRepartitionData)
    {
        return array_merge($baseLineGroupingKey, [
            'tax_repartition_line_id' => $taxRepartitionData['tax_rep']->id,
            'partner_id'              => $baseLine['partner']->id,
            'currency_id'             => $baseLine['currency']->id,
            'group_tax_id'            => $taxData['group']?->id ?? null,
            'analytic_distribution'   => ($taxData['tax']->analytic || ! $taxRepartitionData['tax_rep']->use_in_tax_closing)
                ? $baseLineGroupingKey['analytic_distribution']
                : [],
            'account_id' => $taxRepartitionData['account']?->id ?: $baseLineGroupingKey['account_id'],
            'tax_ids'    => $taxRepartitionData['taxes']->pluck('id')->toArray(),
        ]);
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
    ) {
        $batchingResults = $this->batchForTaxesComputation($taxes, $specialMode);

        $sortedTaxes = $batchingResults['sorted_taxes'];

        $taxesData = [];

        $reverseChargeTaxesData = [];

        foreach ($sortedTaxes as $tax) {
            $taxesData[$tax->id] = $this->prepareTaxExtraData($tax, [
                'group' => $batchingResults['group_per_tax'][$tax->id] ?? null,
                'batch' => $batchingResults['batch_per_tax'][$tax->id],
            ], $specialMode);

            if ($tax->has_negative_factor) {
                $reverseChargeTaxesData[$tax->id] = array_merge($taxesData[$tax->id], [
                    'is_reverse_charge' => true,
                ]);
            }
        }

        $rawBase = $quantity * $priceUnit;

        if ($roundingMethod === 'round_per_line') {
            $rawBase = float_round(
                $rawBase,
                precisionRounding: $precisionRounding ?: Auth::user()->company->currency->rounding
            );
        }

        $evaluationContext = [
            'product'            => $product,
            'price_unit'         => $priceUnit,
            'quantity'           => $quantity,
            'raw_base'           => $rawBase,
            'special_mode'       => $specialMode,
            'precision_rounding' => $precisionRounding,
        ];

        foreach ($sortedTaxes->reverse() as $tax) {
            $this->evalTaxAmount(
                [$tax, 'evalTaxAmountFixedAmount'],
                $tax,
                $taxesData,
                $reverseChargeTaxesData,
                $manualTaxAmounts,
                $rawBase,
                $evaluationContext,
                $sortedTaxes,
                $precisionRounding,
                $roundingMethod,
                $specialMode
            );
        }

        foreach ($sortedTaxes->reverse() as $tax) {
            if ($taxesData[$tax->id]['price_include']) {
                $this->evalTaxAmount(
                    [$tax, 'evalTaxAmountPriceIncluded'],
                    $tax,
                    $taxesData,
                    $reverseChargeTaxesData,
                    $manualTaxAmounts,
                    $rawBase,
                    $evaluationContext,
                    $sortedTaxes,
                    $precisionRounding,
                    $roundingMethod,
                    $specialMode
                );
            }
        }

        foreach ($sortedTaxes as $tax) {
            if (! $taxesData[$tax->id]['price_include']) {
                $this->evalTaxAmount(
                    [$tax, 'evalTaxAmountPriceExcluded'],
                    $tax,
                    $taxesData,
                    $reverseChargeTaxesData,
                    $manualTaxAmounts,
                    $rawBase,
                    $evaluationContext,
                    $sortedTaxes,
                    $precisionRounding,
                    $roundingMethod,
                    $specialMode
                );
            }
        }

        foreach ($sortedTaxes->reverse() as $tax) {
            $taxData = $taxesData[$tax->id];

            if (! array_key_exists('tax_amount', $taxData)) {
                continue;
            }

            if ($manualTaxAmounts && isset($manualTaxAmounts[(string) $tax->id]['base_amount_currency'])) {
                $base = $manualTaxAmounts[(string) $tax->id]['base_amount_currency'];
            } else {
                $totalTaxAmount = 0;

                foreach ($taxData['batch'] as $otherTax) {
                    $totalTaxAmount += $taxesData[$otherTax->id]['tax_amount'];
                }

                $base = $rawBase + $taxData['extra_base_for_base'];

                if ($taxData['price_include'] && in_array($specialMode, [false, 'total_included'], true)) {
                    $base -= $totalTaxAmount;
                }
            }

            $taxData['base'] = $base;

            $taxesData[$tax->id] = $taxData;

            if ($tax->has_negative_factor) {
                $reverseChargeTaxData = $reverseChargeTaxesData[$tax->id];

                $reverseChargeTaxData['base'] = $base;

                $reverseChargeTaxesData[$tax->id] = $reverseChargeTaxData;
            }
        }

        $taxesDataList = [];

        foreach ($taxesData as $taxData) {
            if (array_key_exists('tax_amount', $taxData)) {
                $taxesDataList[] = $taxData;

                $tax = $taxData['tax'];

                if ($tax->has_negative_factor) {
                    $taxesDataList[] = $reverseChargeTaxesData[$tax->id];
                }
            }
        }

        if ($taxesDataList) {
            $totalExcluded = $taxesDataList[0]['base'];

            $taxAmount = array_sum(array_column($taxesDataList, 'tax_amount'));

            $totalIncluded = $totalExcluded + $taxAmount;
        } else {
            $totalIncluded = $totalExcluded = $rawBase;
        }

        $taxesDataResult = [];

        foreach ($taxesDataList as $taxData) {
            $taxesDataResult[] = [
                'tax'               => $taxData['tax'],
                'group'             => $batchingResults['group_per_tax'][$taxData['tax']->id] ?? new Tax,
                'batch'             => $batchingResults['batch_per_tax'][$taxData['tax']->id],
                'tax_amount'        => $taxData['tax_amount'],
                'base_amount'       => $taxData['base'],
                'is_reverse_charge' => $taxData['is_reverse_charge'] ?? false,
            ];
        }

        return [
            'total_excluded' => $totalExcluded,
            'total_included' => $totalIncluded,
            'taxes_data'     => $taxesDataResult,
        ];
    }

    protected function batchForTaxesComputation(mixed $taxes, string|bool $specialMode = false): array
    {
        $results = [
            'batch_per_tax' => [],
            'group_per_tax' => [],
            'sorted_taxes'  => collect(),
        ];

        $taxes = $taxes->sortBy('sequence');

        foreach ($taxes as $tax) {
            if ($tax->amount_type === AmountType::GROUP) {
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
        $getTaxBefore = function ($taxes, $tax, &$taxesData) {
            foreach ($taxes as $taxBefore) {
                if (collect($taxesData[$tax->id]['batch'])->pluck('id')->contains($taxBefore->id)) {
                    break;
                }

                yield $taxBefore;
            }
        };

        $getTaxAfter = function ($taxes, $tax, &$taxesData) {
            foreach ($taxes->reverse() as $taxAfter) {
                if (collect($taxesData[$tax->id]['batch'])->pluck('id')->contains($taxAfter->id)) {
                    break;
                }

                yield $taxAfter;
            }
        };

        $addExtraBase = function ($otherTax, int $sign, &$taxesData, $tax) {
            if (! isset($taxesData[$tax->id]['tax_amount'])) {
                return;
            }

            $taxAmount = $taxesData[$tax->id]['tax_amount'];

            if (! isset($taxesData[$otherTax->id]['tax_amount'])) {
                $taxesData[$otherTax->id]['extra_base_for_tax'] += $sign * $taxAmount;
            }

            $taxesData[$otherTax->id]['extra_base_for_base'] += $sign * $taxAmount;
        };

        if ($tax->price_include) {
            if ($specialMode === false || $specialMode === 'total_included') {
                if (! $tax->include_base_amount) {
                    foreach ($getTaxAfter($taxes, $tax, $taxesData) as $otherTax) {
                        if ($otherTax->price_include) {
                            $addExtraBase($otherTax, -1, $taxesData, $tax);
                        }
                    }
                }

                foreach ($getTaxBefore($taxes, $tax, $taxesData) as $otherTax) {
                    $addExtraBase($otherTax, -1, $taxesData, $tax);
                }
            } else {
                foreach ($getTaxAfter($taxes, $tax, $taxesData) as $otherTax) {
                    if (! $otherTax->price_include || $tax->include_base_amount) {
                        $addExtraBase($otherTax, 1, $taxesData, $tax);
                    }
                }
            }
        } else {
            if ($specialMode === false || $specialMode === 'total_excluded') {
                if ($tax->include_base_amount) {
                    foreach ($getTaxAfter($taxes, $tax, $taxesData) as $otherTax) {
                        if ($otherTax->is_base_affected) {
                            $addExtraBase($otherTax, 1, $taxesData, $tax);
                        }
                    }
                }
            } else {
                if (! $tax->include_base_amount) {
                    foreach ($getTaxAfter($taxes, $tax, $taxesData) as $otherTax) {
                        $addExtraBase($otherTax, -1, $taxesData, $tax);
                    }
                }

                foreach ($getTaxBefore($taxes, $tax, $taxesData) as $otherTax) {
                    $addExtraBase($otherTax, -1, $taxesData, $tax);
                }
            }
        }
    }

    protected function prepareTaxExtraData($tax, $args = [], $specialMode = false)
    {
        if ($specialMode === 'total_included') {
            $priceInclude = true;
        } elseif ($specialMode === 'total_excluded') {
            $priceInclude = false;
        } else {
            $priceInclude = $tax->price_include;
        }

        return array_merge($args, [
            'tax'                 => $tax,
            'price_include'       => $priceInclude,
            'extra_base_for_tax'  => 0.0,
            'extra_base_for_base' => 0.0,
        ]);
    }

    protected function evalTaxAmount(
        callable $taxAmountFunction,
        $tax,
        &$taxesData,
        &$reverseChargeTaxesData,
        $manualTaxAmounts,
        &$rawBase,
        &$evaluationContext,
        $sortedTaxes,
        $precisionRounding,
        $roundingMethod,
        $specialMode
    ) {
        $addTaxAmountToResults = function (
            $tax,
            float $taxAmount
        ) use (
            &$taxesData,
            &$reverseChargeTaxesData,
            $sortedTaxes,
            $precisionRounding,
            $roundingMethod,
            $specialMode
        ) {
            $taxesData[$tax->id]['tax_amount'] = $taxAmount;

            if ($roundingMethod === 'round_per_line') {
                $taxesData[$tax->id]['tax_amount'] = float_round(
                    $taxesData[$tax->id]['tax_amount'],
                    precisionRounding: $precisionRounding ?? $tax->company->currency->rounding
                );
            }

            if ($tax->has_negative_factor) {
                $reverseChargeTaxesData[$tax->id]['tax_amount'] = -$taxesData[$tax->id]['tax_amount'];
            }

            $this->propagateExtraTaxesBase($sortedTaxes, $tax, $taxesData, $specialMode);
        };

        $isAlreadyComputed = array_key_exists('tax_amount', $taxesData[$tax->id]);

        if ($isAlreadyComputed) {
            return;
        }

        if ($manualTaxAmounts && isset($manualTaxAmounts[(string) $tax->id])) {
            $taxAmount = $manualTaxAmounts[(string) $tax->id]['tax_amount_currency'];
        } else {
            $taxAmount = $taxAmountFunction(
                $taxesData[$tax->id]['batch'],
                $rawBase + $taxesData[$tax->id]['extra_base_for_tax'],
                $evaluationContext
            );
        }

        if ($taxAmount !== null) {
            $addTaxAmountToResults($tax, $taxAmount);
        }
    }
}
