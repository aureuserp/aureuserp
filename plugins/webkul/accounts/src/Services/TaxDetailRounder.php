<?php

namespace Webkul\Account\Services;

use Webkul\Account\Enums\DocumentType;
use Webkul\Support\Models\Currency;

class TaxDetailRounder
{
    public function roundTaxDetails(array $baseLines, $company, array $taxLines = []): array
    {
        $totalPerTax = [];

        $totalPerBase = [];

        $taxKeysPerTaxLineKey = [];

        foreach ($baseLines as $index => $baseLine) {
            $baseLines[$index] = $this->roundSingleBaseLine(
                $baseLine,
                $index,
                $company,
                $totalPerTax,
                $totalPerBase,
                $taxKeysPerTaxLineKey
            );
        }

        $this->roundAccumulatedTotals($totalPerTax, $totalPerBase, $company);

        if ($taxLines !== []) {
            $this->alignWithExistingTaxLines($taxLines, $totalPerTax, $taxKeysPerTaxLineKey);
        }

        $this->distributeTaxRoundingDeltas($baseLines, $totalPerTax);

        $this->distributeBaseRoundingDeltas($baseLines, $totalPerTax, $totalPerBase, $company);

        $this->distributeUntaxedBaseDeltas($baseLines, $totalPerBase, $company);

        return $baseLines;
    }

    protected function roundSingleBaseLine(
        array $baseLine,
        int|string $baseLineIndex,
        $company,
        array &$totalPerTax,
        array &$totalPerBase,
        array &$taxKeysPerTaxLineKey
    ): array {
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

            $taxKey = json_encode([$tax->id ?? null, $currency->id, $baseLine['is_refund'], $taxData['is_reverse_charge']]);

            $taxLineKey = json_encode([$tax->id ?? null, $currency->id, $baseLine['is_refund']]);

            $taxKeysPerTaxLineKey[$taxLineKey][$taxKey] = true;

            $this->ensureTaxBucket($totalPerTax, $taxKey);

            foreach ([
                'tax_amount_currency',
                'raw_tax_amount_currency',
                'tax_amount',
                'raw_tax_amount',
                'base_amount_currency',
                'raw_base_amount_currency',
                'base_amount',
                'raw_base_amount',
            ] as $field) {
                $totalPerTax[$taxKey][$field] += $taxData[$field];
            }

            if (! $baseLine['special_type']) {
                $totalPerTax[$taxKey]['base_lines'][] = $baseLineIndex;
            }

            if ($index === 0) {
                $baseKey = json_encode([$currency->id, $baseLine['is_refund']]);

                $this->ensureBaseBucket($totalPerBase, $baseKey);

                foreach (['base_amount_currency', 'raw_base_amount_currency', 'base_amount', 'raw_base_amount'] as $field) {
                    $totalPerBase[$baseKey][$field] += $taxData[$field];
                }

                if (! $baseLine['special_type']) {
                    $totalPerBase[$baseKey]['base_lines'][] = $baseLineIndex;
                }
            }

            $taxesData[$index] = $taxData;
        }

        if ($taxesData === []) {
            $this->accumulateUntaxedBaseLine($baseLine, $baseLineIndex, $taxDetails, $totalPerTax, $totalPerBase);
        }

        $taxDetails['taxes_data'] = $taxesData;

        $baseLine['tax_details'] = $taxDetails;

        return $baseLine;
    }

    protected function accumulateUntaxedBaseLine(
        array $baseLine,
        int|string $baseLineIndex,
        array $taxDetails,
        array &$totalPerTax,
        array &$totalPerBase
    ): void {
        $currency = $baseLine['currency'];

        $taxKey = json_encode([null, $currency->id, $baseLine['is_refund'], false]);

        $this->ensureTaxBucket($totalPerTax, $taxKey);

        $totalPerTax[$taxKey]['base_amount_currency'] += $taxDetails['total_excluded_currency'];
        $totalPerTax[$taxKey]['raw_base_amount_currency'] += $taxDetails['raw_total_excluded_currency'];
        $totalPerTax[$taxKey]['base_amount'] += $taxDetails['total_excluded'];
        $totalPerTax[$taxKey]['raw_base_amount'] += $taxDetails['raw_total_excluded'];

        if (! $baseLine['special_type']) {
            $totalPerTax[$taxKey]['base_lines'][] = $baseLineIndex;
        }

        $baseKey = json_encode([$currency->id, $baseLine['is_refund']]);

        $this->ensureBaseBucket($totalPerBase, $baseKey);

        $totalPerBase[$baseKey]['base_amount_currency'] += $taxDetails['total_excluded_currency'];
        $totalPerBase[$baseKey]['raw_base_amount_currency'] += $taxDetails['raw_total_excluded_currency'];
        $totalPerBase[$baseKey]['base_amount'] += $taxDetails['total_excluded'];
        $totalPerBase[$baseKey]['raw_base_amount'] += $taxDetails['raw_total_excluded'];

        if (! $baseLine['special_type']) {
            $totalPerBase[$baseKey]['base_lines'][] = $baseLineIndex;
        }
    }

    protected function roundAccumulatedTotals(array &$totalPerTax, array &$totalPerBase, $company): void
    {
        foreach ($totalPerTax as $key => $amounts) {
            $currency = Currency::find(json_decode($key, true)[1]);

            $amounts['raw_tax_amount_currency'] = $currency->round($amounts['raw_tax_amount_currency']);
            $amounts['raw_tax_amount'] = $company->currency->round($amounts['raw_tax_amount']);
            $amounts['raw_base_amount_currency'] = $currency->round($amounts['raw_base_amount_currency']);
            $amounts['raw_base_amount'] = $company->currency->round($amounts['raw_base_amount']);

            $totalPerTax[$key] = $amounts;
        }

        foreach ($totalPerBase as $key => $amounts) {
            $currency = Currency::find(json_decode($key, true)[0]);

            $amounts['raw_base_amount_currency'] = $currency->round($amounts['raw_base_amount_currency']);
            $amounts['raw_base_amount'] = $company->currency->round($amounts['raw_base_amount']);

            $totalPerBase[$key] = $amounts;
        }
    }

    protected function alignWithExistingTaxLines(array $taxLines, array &$totalPerTax, array $taxKeysPerTaxLineKey): void
    {
        $totalPerTaxLineKey = [];

        foreach ($taxLines as $taxLine) {
            $repartition = $taxLine['taxRepartitionLine'];

            $taxLineKey = json_encode([
                $repartition->tax_id,
                $taxLine['currency']->id,
                $repartition->document_type === DocumentType::REFUND,
            ]);

            if (! isset($totalPerTaxLineKey[$taxLineKey])) {
                $totalPerTaxLineKey[$taxLineKey] = ['tax_amount_currency' => 0.0, 'tax_amount' => 0.0];
            }

            $totalPerTaxLineKey[$taxLineKey]['tax_amount_currency'] += $taxLine['sign'] * $taxLine['amount_currency'];
            $totalPerTaxLineKey[$taxLineKey]['tax_amount'] += $taxLine['sign'] * $taxLine['balance'];
        }

        foreach ($totalPerTaxLineKey as $taxLineKey => $lineAmounts) {
            $taxKeys = array_keys($taxKeysPerTaxLineKey[$taxLineKey] ?? []);

            if ($taxKeys === []) {
                continue;
            }

            $rawTaxAmountCurrency = 0.0;

            $rawTaxAmount = 0.0;

            foreach ($taxKeys as $taxKey) {
                $rawTaxAmountCurrency += $totalPerTax[$taxKey]['raw_tax_amount_currency'];
                $rawTaxAmount += $totalPerTax[$taxKey]['raw_tax_amount'];
            }

            $dominantKey = $this->keyWithLargest($taxKeys, fn ($key) => $totalPerTax[$key]['raw_tax_amount_currency']);

            if ($dominantKey === null) {
                continue;
            }

            $totalPerTax[$dominantKey]['raw_tax_amount_currency'] += $lineAmounts['tax_amount_currency'] - $rawTaxAmountCurrency;
            $totalPerTax[$dominantKey]['raw_tax_amount'] += $lineAmounts['tax_amount'] - $rawTaxAmount;
        }
    }

    protected function distributeTaxRoundingDeltas(array &$baseLines, array &$totalPerTax): void
    {
        foreach ($totalPerTax as $key => $amounts) {
            if ($amounts['base_lines'] === []) {
                continue;
            }

            $referenceIndex = $this->largestBaseLineIndex($baseLines, $amounts['base_lines']);

            $totalPerTax[$key]['reference_base_line_index'] = $referenceIndex;

            [$taxId, , , $isReverseCharge] = json_decode($key, true);

            if ($taxId === null) {
                continue;
            }

            $deltaCurrency = $amounts['raw_tax_amount_currency'] - $amounts['tax_amount_currency'];

            $delta = $amounts['raw_tax_amount'] - $amounts['tax_amount'];

            foreach ($baseLines[$referenceIndex]['tax_details']['taxes_data'] as $taxIndex => $taxData) {
                if ($taxData['tax']->id !== $taxId || $taxData['is_reverse_charge'] !== $isReverseCharge) {
                    continue;
                }

                $totalPerTax[$key]['reference_tax_data_index'] = $taxIndex;

                $baseLines[$referenceIndex]['tax_details']['taxes_data'][$taxIndex]['tax_amount_currency'] += $deltaCurrency;
                $baseLines[$referenceIndex]['tax_details']['taxes_data'][$taxIndex]['tax_amount'] += $delta;

                break;
            }
        }
    }

    protected function distributeBaseRoundingDeltas(array &$baseLines, array $totalPerTax, array &$totalPerBase, $company): void
    {
        foreach ($totalPerTax as $key => $amounts) {
            if (! isset($amounts['reference_base_line_index'])) {
                continue;
            }

            $referenceIndex = $amounts['reference_base_line_index'];

            $currency = Currency::find(json_decode($key, true)[1]);

            $deltaCurrency = $amounts['raw_base_amount_currency'] - $amounts['base_amount_currency'];

            $delta = $amounts['raw_base_amount'] - $amounts['base_amount'];

            if ($currency->isZero($deltaCurrency) && $company->currency->isZero($delta)) {
                continue;
            }

            if (isset($amounts['reference_tax_data_index'])) {
                $taxIndex = $amounts['reference_tax_data_index'];

                $baseLines[$referenceIndex]['tax_details']['taxes_data'][$taxIndex]['base_amount_currency'] += $deltaCurrency;
                $baseLines[$referenceIndex]['tax_details']['taxes_data'][$taxIndex]['base_amount'] += $delta;

                continue;
            }

            $baseLines[$referenceIndex]['tax_details']['delta_total_excluded_currency'] += $deltaCurrency;
            $baseLines[$referenceIndex]['tax_details']['delta_total_excluded'] += $delta;

            $baseKey = json_encode([$currency->id, $baseLines[$referenceIndex]['is_refund']]);

            if (isset($totalPerBase[$baseKey])) {
                $totalPerBase[$baseKey]['base_amount_currency'] += $deltaCurrency;
                $totalPerBase[$baseKey]['base_amount'] += $delta;
            }
        }
    }

    protected function distributeUntaxedBaseDeltas(array &$baseLines, array $totalPerBase, $company): void
    {
        foreach ($totalPerBase as $key => $amounts) {
            if ($amounts['base_lines'] === []) {
                continue;
            }

            $referenceIndex = $this->largestBaseLineIndex($baseLines, $amounts['base_lines']);

            $currency = Currency::find(json_decode($key, true)[0]);

            $deltaCurrency = $amounts['raw_base_amount_currency'] - $amounts['base_amount_currency'];

            $delta = $amounts['raw_base_amount'] - $amounts['base_amount'];

            if ($currency->isZero($deltaCurrency) && $company->currency->isZero($delta)) {
                continue;
            }

            $baseLines[$referenceIndex]['tax_details']['delta_total_excluded_currency'] += $deltaCurrency;
            $baseLines[$referenceIndex]['tax_details']['delta_total_excluded'] += $delta;
        }
    }

    protected function largestBaseLineIndex(array $baseLines, array $candidateIndexes): int|string|null
    {
        return $this->keyWithLargest(
            $candidateIndexes,
            fn ($index) => $baseLines[$index]['tax_details']['total_included_currency']
        );
    }

    protected function keyWithLargest(array $keys, callable $valueOf): mixed
    {
        $winner = null;

        $best = null;

        foreach ($keys as $key) {
            $value = $valueOf($key);

            if ($best === null || $value > $best) {
                $best = $value;

                $winner = $key;
            }
        }

        return $winner;
    }

    protected function ensureTaxBucket(array &$totalPerTax, string $key): void
    {
        $totalPerTax[$key] ??= [
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

    protected function ensureBaseBucket(array &$totalPerBase, string $key): void
    {
        $totalPerBase[$key] ??= [
            'base_amount_currency'     => 0.0,
            'base_amount'              => 0.0,
            'raw_base_amount_currency' => 0.0,
            'raw_base_amount'          => 0.0,
            'base_lines'               => [],
        ];
    }
}
