<?php

namespace Webkul\Account\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Webkul\Account\Enums\AmountType;
use Webkul\Account\Models\Tax;
use Webkul\Account\Settings\TaxesSettings;

class TaxComputer
{
    public function addTaxDetailsToAll(array $baseLines, $company): array
    {
        return array_map(fn (array $baseLine) => $this->withTaxDetails($baseLine, $company), $baseLines);
    }

    public function withTaxDetails(array $baseLine, $company, ?string $roundingMethod = null): array
    {
        $defaultRoundingMethod = (new TaxesSettings)->tax_calculation_rounding_method;

        $computation = $this->computeTaxes(
            taxes: $baseLine['taxes'],
            priceUnit: $baseLine['price_unit'] * (1 - ($baseLine['discount'] / 100)),
            quantity: $baseLine['quantity'],
            precisionRounding: $baseLine['currency']->rounding ?? 2,
            roundingMethod: $roundingMethod ?? $defaultRoundingMethod,
            product: $baseLine['product'],
            specialMode: $baseLine['special_mode'],
            manualTaxAmounts: $baseLine['manual_tax_amounts'],
        );

        $rate = $baseLine['rate'] ?: 1.0;

        $perLine = $defaultRoundingMethod === 'round_per_line';

        $taxDetails = [
            'raw_total_excluded_currency' => $computation['total_excluded'],
            'raw_total_excluded'          => $this->toCompany($computation['total_excluded'], $rate, $company, $perLine),
            'raw_total_included_currency' => $computation['total_included'],
            'raw_total_included'          => $this->toCompany($computation['total_included'], $rate, $company, $perLine),
            'taxes_data'                  => [],
        ];

        foreach ($computation['taxes_data'] as $taxData) {
            $taxDetails['taxes_data'][] = array_merge($taxData, [
                'raw_tax_amount_currency'  => $taxData['tax_amount'],
                'raw_tax_amount'           => $this->toCompany($taxData['tax_amount'], $rate, $company, $perLine),
                'raw_base_amount_currency' => $taxData['base_amount'],
                'raw_base_amount'          => $this->toCompany($taxData['base_amount'], $rate, $company, $perLine),
            ]);
        }

        $baseLine['tax_details'] = $taxDetails;

        return $baseLine;
    }

    protected function toCompany(float $amount, float $rate, $company, bool $roundPerLine): float
    {
        $converted = $rate ? $amount / $rate : 0.0;

        return $roundPerLine ? $company->currency->round($converted) : $converted;
    }

    public function computeTaxes(
        mixed $taxes,
        float $priceUnit,
        float $quantity,
        float $precisionRounding = 0.01,
        string $roundingMethod = 'round_per_line',
        mixed $product = null,
        string|bool $specialMode = false,
        ?array $manualTaxAmounts = null,
    ): array {
        $batching = $this->flattenTaxGroups($taxes);

        $sortedTaxes = $batching['sorted_taxes'];

        $taxesData = [];

        $reverseChargeData = [];

        foreach ($sortedTaxes as $tax) {
            $taxesData[$tax->id] = $this->initialTaxState($tax, [
                'group' => $batching['group_per_tax'][$tax->id] ?? null,
                'batch' => $batching['batch_per_tax'][$tax->id],
            ], $specialMode);

            if ($tax->has_negative_factor) {
                $reverseChargeData[$tax->id] = array_merge($taxesData[$tax->id], ['is_reverse_charge' => true]);
            }
        }

        $rawBase = $quantity * $priceUnit;

        if ($roundingMethod === 'round_per_line') {
            $rawBase = float_round(
                $rawBase,
                precisionRounding: $precisionRounding ?: Auth::user()->company->currency->rounding
            );
        }

        $context = [
            'product'            => $product,
            'price_unit'         => $priceUnit,
            'quantity'           => $quantity,
            'raw_base'           => $rawBase,
            'special_mode'       => $specialMode,
            'precision_rounding' => $precisionRounding,
        ];

        $resolve = function (string $method, $tax) use (
            &$taxesData,
            &$reverseChargeData,
            &$rawBase,
            &$context,
            $manualTaxAmounts,
            $sortedTaxes,
            $precisionRounding,
            $roundingMethod,
            $specialMode
        ) {
            $this->resolveTaxAmount(
                [$tax, $method],
                $tax,
                $taxesData,
                $reverseChargeData,
                $manualTaxAmounts,
                $rawBase,
                $context,
                $sortedTaxes,
                $precisionRounding,
                $roundingMethod,
                $specialMode
            );
        };

        foreach ($sortedTaxes->reverse() as $tax) {
            $resolve('evalTaxAmountFixedAmount', $tax);
        }

        foreach ($sortedTaxes->reverse() as $tax) {
            if ($taxesData[$tax->id]['price_include']) {
                $resolve('evalTaxAmountPriceIncluded', $tax);
            }
        }

        foreach ($sortedTaxes as $tax) {
            if (! $taxesData[$tax->id]['price_include']) {
                $resolve('evalTaxAmountPriceExcluded', $tax);
            }
        }

        $this->resolveBaseAmounts($sortedTaxes, $taxesData, $reverseChargeData, $rawBase, $manualTaxAmounts, $specialMode);

        return $this->summarize($taxesData, $reverseChargeData, $batching, $rawBase);
    }

    protected function resolveBaseAmounts(
        Collection $sortedTaxes,
        array &$taxesData,
        array &$reverseChargeData,
        float $rawBase,
        ?array $manualTaxAmounts,
        string|bool $specialMode
    ): void {
        foreach ($sortedTaxes->reverse() as $tax) {
            $taxData = $taxesData[$tax->id];

            if (! array_key_exists('tax_amount', $taxData)) {
                continue;
            }

            if ($manualTaxAmounts && isset($manualTaxAmounts[(string) $tax->id]['base_amount_currency'])) {
                $base = $manualTaxAmounts[(string) $tax->id]['base_amount_currency'];
            } else {
                $batchTaxAmount = 0;

                foreach ($taxData['batch'] as $otherTax) {
                    $batchTaxAmount += $taxesData[$otherTax->id]['tax_amount'];
                }

                $base = $rawBase + $taxData['extra_base_for_base'];

                if ($taxData['price_include'] && in_array($specialMode, [false, 'total_included'], true)) {
                    $base -= $batchTaxAmount;
                }
            }

            $taxData['base'] = $base;

            $taxesData[$tax->id] = $taxData;

            if ($tax->has_negative_factor) {
                $reverseChargeData[$tax->id]['base'] = $base;
            }
        }
    }

    protected function summarize(array $taxesData, array $reverseChargeData, array $batching, float $rawBase): array
    {
        $computed = [];

        foreach ($taxesData as $taxData) {
            if (! array_key_exists('tax_amount', $taxData)) {
                continue;
            }

            $computed[] = $taxData;

            if ($taxData['tax']->has_negative_factor) {
                $computed[] = $reverseChargeData[$taxData['tax']->id];
            }
        }

        if ($computed) {
            $totalExcluded = $computed[0]['base'];

            $totalIncluded = $totalExcluded + array_sum(array_column($computed, 'tax_amount'));
        } else {
            $totalIncluded = $totalExcluded = $rawBase;
        }

        return [
            'total_excluded' => $totalExcluded,
            'total_included' => $totalIncluded,
            'taxes_data'     => array_map(fn ($taxData) => [
                'tax'               => $taxData['tax'],
                'group'             => $batching['group_per_tax'][$taxData['tax']->id] ?? new Tax,
                'batch'             => $batching['batch_per_tax'][$taxData['tax']->id],
                'tax_amount'        => $taxData['tax_amount'],
                'base_amount'       => $taxData['base'],
                'is_reverse_charge' => $taxData['is_reverse_charge'] ?? false,
            ], $computed),
        ];
    }

    public function flattenTaxGroups(mixed $taxes): array
    {
        $sorted = collect();

        $groupPerTax = [];

        foreach ($taxes->sortBy('sort') as $tax) {
            if ($tax->amount_type !== AmountType::GROUP) {
                $sorted->push($tax);

                continue;
            }

            $children = $tax->childrenTaxes()->orderBy('sort')->get();

            $sorted = $sorted->merge($children);

            foreach ($children as $child) {
                $groupPerTax[$child->id] = $tax;
            }
        }

        $batchPerTax = [];

        foreach ($sorted as $tax) {
            $batchPerTax[$tax->id] = [$tax];
        }

        return [
            'batch_per_tax' => $batchPerTax,
            'group_per_tax' => $groupPerTax,
            'sorted_taxes'  => $sorted,
        ];
    }

    public function propagateBaseAdjustments(Collection $taxes, $tax, array &$taxesData, string|bool $specialMode = false): void
    {
        $before = function (Collection $taxes, $tax, &$taxesData) {
            foreach ($taxes as $candidate) {
                if (collect($taxesData[$tax->id]['batch'])->pluck('id')->contains($candidate->id)) {
                    break;
                }

                yield $candidate;
            }
        };

        $after = function (Collection $taxes, $tax, &$taxesData) {
            foreach ($taxes->reverse() as $candidate) {
                if (collect($taxesData[$tax->id]['batch'])->pluck('id')->contains($candidate->id)) {
                    break;
                }

                yield $candidate;
            }
        };

        $shift = function ($otherTax, int $sign) use (&$taxesData, $tax) {
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
                    foreach ($after($taxes, $tax, $taxesData) as $otherTax) {
                        if ($otherTax->price_include) {
                            $shift($otherTax, -1);
                        }
                    }
                }

                foreach ($before($taxes, $tax, $taxesData) as $otherTax) {
                    $shift($otherTax, -1);
                }

                return;
            }

            foreach ($after($taxes, $tax, $taxesData) as $otherTax) {
                if (! $otherTax->price_include || $tax->include_base_amount) {
                    $shift($otherTax, 1);
                }
            }

            return;
        }

        if ($specialMode === false || $specialMode === 'total_excluded') {
            if (! $tax->include_base_amount) {
                return;
            }

            foreach ($after($taxes, $tax, $taxesData) as $otherTax) {
                if ($otherTax->is_base_affected) {
                    $shift($otherTax, 1);
                }
            }

            return;
        }

        if (! $tax->include_base_amount) {
            foreach ($after($taxes, $tax, $taxesData) as $otherTax) {
                $shift($otherTax, -1);
            }
        }

        foreach ($before($taxes, $tax, $taxesData) as $otherTax) {
            $shift($otherTax, -1);
        }
    }

    protected function initialTaxState($tax, array $context, string|bool $specialMode): array
    {
        $priceInclude = match ($specialMode) {
            'total_included' => true,
            'total_excluded' => false,
            default          => $tax->price_include,
        };

        return array_merge($context, [
            'tax'                 => $tax,
            'price_include'       => $priceInclude,
            'extra_base_for_tax'  => 0.0,
            'extra_base_for_base' => 0.0,
        ]);
    }

    protected function resolveTaxAmount(
        callable $amountResolver,
        $tax,
        array &$taxesData,
        array &$reverseChargeData,
        ?array $manualTaxAmounts,
        float &$rawBase,
        array &$context,
        Collection $sortedTaxes,
        float $precisionRounding,
        string $roundingMethod,
        string|bool $specialMode
    ): void {
        if (array_key_exists('tax_amount', $taxesData[$tax->id])) {
            return;
        }

        if ($manualTaxAmounts && isset($manualTaxAmounts[(string) $tax->id])) {
            $taxAmount = $manualTaxAmounts[(string) $tax->id]['tax_amount_currency'];
        } else {
            $taxAmount = $amountResolver(
                $taxesData[$tax->id]['batch'],
                $rawBase + $taxesData[$tax->id]['extra_base_for_tax'],
                $context
            );
        }

        if ($taxAmount === null) {
            return;
        }

        $taxesData[$tax->id]['tax_amount'] = $roundingMethod === 'round_per_line'
            ? float_round($taxAmount, precisionRounding: $precisionRounding)
            : $taxAmount;

        if ($tax->has_negative_factor) {
            $reverseChargeData[$tax->id]['tax_amount'] = -$taxesData[$tax->id]['tax_amount'];
        }

        $this->propagateBaseAdjustments($sortedTaxes, $tax, $taxesData, $specialMode);
    }
}
