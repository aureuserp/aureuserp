<?php

namespace Webkul\Account\Services;

use Illuminate\Database\Eloquent\Model;
use Webkul\Account\Enums\RepartitionType;
use Webkul\Support\Models\Currency;

class TaxAccountingMapper
{
    public function withAccountingData(array $baseLine, $company, bool $useRawAmounts = false): array
    {
        $companyCurrency = $company->currency;

        $currency = $baseLine['currency'] ?? $companyCurrency;

        $repartitionRelation = $baseLine['is_refund'] ? 'refundRepartitionLines' : 'invoiceRepartitionLines';

        $taxesData = $baseLine['tax_details']['taxes_data'] ?? [];

        foreach ($taxesData as $taxIndex => $taxData) {
            $repartitions = $this->repartitionsFor($taxData, $repartitionRelation);

            $repartitionData = $this->buildRepartitionData(
                $taxData,
                $repartitions['lines'],
                $repartitions['sign'],
                $baseLine,
                $currency,
                $useRawAmounts
            );

            $this->absorbRoundingErrors($taxData, $repartitionData, $currency, $companyCurrency, $useRawAmounts);

            $taxesData[$taxIndex]['tax_reps_data'] = $repartitionData;
        }

        $taxesData = $this->attachGroupingKeys($taxesData, $baseLine);

        $baseLine['tax_details']['taxes_data'] = $taxesData;

        return $baseLine;
    }

    protected function repartitionsFor(array $taxData, string $relation): array
    {
        $tax = $taxData['tax'];

        if ($taxData['is_reverse_charge']) {
            return [
                'lines' => $tax->{$relation}->filter(
                    fn ($line) => $line->repartition_type === RepartitionType::TAX && $line->factor < 0.0
                ),
                'sign' => -1.0,
            ];
        }

        return [
            'lines' => $tax->{$relation}->filter(
                fn ($line) => $line->repartition_type === RepartitionType::TAX && $line->factor >= 0.0
            ),
            'sign' => 1.0,
        ];
    }

    protected function buildRepartitionData(
        array $taxData,
        $repartitions,
        float $sign,
        array $baseLine,
        $currency,
        bool $useRawAmounts
    ): array {
        $entries = [];

        foreach ($repartitions as $repartition) {
            $taxAmountCurrency = $useRawAmounts
                ? ($taxData['raw_tax_amount_currency'] ?? 0.0)
                : ($taxData['tax_amount_currency'] ?? 0.0);

            $entries[] = [
                'tax_rep'             => $repartition,
                'tax_amount_currency' => $currency->round($taxAmountCurrency * $repartition->factor * $sign),
                'tax_amount'          => $currency->round($taxData['tax_amount'] * $repartition->factor * $sign),
                'account'             => $repartition->account ?? $baseLine['account'],
            ];
        }

        usort($entries, function ($a, $b) {
            $byCurrency = abs($b['tax_amount_currency']) <=> abs($a['tax_amount_currency']);

            return $byCurrency !== 0 ? $byCurrency : abs($b['tax_amount']) <=> abs($a['tax_amount']);
        });

        return $entries;
    }

    protected function absorbRoundingErrors(
        array $taxData,
        array &$repartitionData,
        $currency,
        $companyCurrency,
        bool $useRawAmounts
    ): void {
        if ($repartitionData === []) {
            return;
        }

        foreach ([['tax_amount_currency', $currency], ['tax_amount', $companyCurrency]] as [$field, $fieldCurrency]) {
            $expected = $useRawAmounts
                ? ($taxData["raw_{$field}"] ?? 0.0)
                : ($taxData[$field] ?? 0.0);

            $distributed = array_sum(array_column($repartitionData, $field));

            $totalError = $expected - $distributed;

            $steps = (int) round(abs($totalError / $fieldCurrency->rounding));

            if ($steps === 0) {
                continue;
            }

            $increment = $totalError / $steps;

            for ($step = 0, $index = 0; $step < $steps; $step++) {
                $repartitionData[$index][$field] += $increment;

                $index = ($index + 1) % count($repartitionData);
            }
        }
    }

    protected function attachGroupingKeys(array $taxesData, array $baseLine): array
    {
        $subsequentTaxes = collect();

        foreach (array_reverse($taxesData, true) as $taxIndex => $taxData) {
            $tax = $taxData['tax'];

            foreach ($taxData['tax_reps_data'] as $repIndex => $repartitionData) {
                $repartitionData['taxes'] = $tax->include_base_amount ? collect()->merge($subsequentTaxes) : collect();

                $baseGroupingKey = [
                    'partner_id'            => $baseLine['partner']->id,
                    'currency_id'           => $baseLine['currency']->id,
                    'analytic_distribution' => $baseLine['analytic_distribution'],
                    'account_id'            => $baseLine['account']->id,
                    'tax_ids'               => $baseLine instanceof Model
                        ? [$baseLine->id]
                        : $baseLine['taxes']->pluck('id')->toArray(),
                ];

                $repartitionData['grouping_key'] = $this->repartitionGroupingKey(
                    $baseLine,
                    $baseGroupingKey,
                    $taxData,
                    $repartitionData
                );

                $taxesData[$taxIndex]['tax_reps_data'][$repIndex] = $repartitionData;
            }

            if ($tax->is_base_affected) {
                $subsequentTaxes = $subsequentTaxes->push($tax);
            }
        }

        return $taxesData;
    }

    public function repartitionGroupingKey(array $baseLine, array $baseGroupingKey, array $taxData, array $repartitionData): array
    {
        return array_merge($baseGroupingKey, [
            'tax_repartition_line_id' => $repartitionData['tax_rep']->id,
            'partner_id'              => $baseLine['partner']->id,
            'currency_id'             => $baseLine['currency']->id,
            'group_tax_id'            => $taxData['group']?->id ?? null,
            'analytic_distribution'   => ($taxData['tax']->analytic || ! $repartitionData['tax_rep']->use_in_tax_closing)
                ? $baseGroupingKey['analytic_distribution']
                : [],
            'account_id' => $repartitionData['account']?->id ?: $baseGroupingKey['account_id'],
            'tax_ids'    => $repartitionData['taxes']->pluck('id')->toArray(),
        ]);
    }

    public function buildTaxLineChanges(array $baseLines, $company, array $taxLines = []): array
    {
        $pending = [];

        $baseLinesToUpdate = [];

        foreach ($baseLines as $baseLine) {
            $taxDetails = $baseLine['tax_details'];

            $baseLinesToUpdate[] = array_merge($baseLine, [
                'amount_currency' => $baseLine['sign'] * ($taxDetails['total_excluded_currency'] + $taxDetails['delta_total_excluded_currency']),
                'balance'         => $baseLine['sign'] * ($taxDetails['total_excluded'] + $taxDetails['delta_total_excluded']),
            ]);

            foreach ($taxDetails['taxes_data'] as $taxData) {
                foreach ($taxData['tax_reps_data'] as $repartitionData) {
                    $this->accumulateTaxLine($pending, $baseLine, $taxData, $repartitionData);
                }
            }
        }

        $pending = array_filter($pending, fn ($entry) => $this->isMaterial($entry, $company));

        [$toUpdate, $toDelete, $pending] = $this->matchExistingTaxLines($taxLines, $pending);

        $toAdd = [];

        foreach ($pending as $entry) {
            $groupingKey = $entry['grouping_key'];

            unset($entry['grouping_key']);

            $toAdd[] = array_merge($groupingKey, $entry);
        }

        return [
            'tax_lines_to_add'     => $toAdd,
            'tax_lines_to_delete'  => $toDelete,
            'tax_lines_to_update'  => $toUpdate,
            'base_lines_to_update' => $baseLinesToUpdate,
        ];
    }

    protected function accumulateTaxLine(array &$pending, array $baseLine, array $taxData, array $repartitionData): void
    {
        $groupingKey = json_encode($repartitionData['grouping_key']);

        $pending[$groupingKey] ??= [
            'grouping_key'    => $repartitionData['grouping_key'],
            'tax_base_amount' => 0.0,
            'amount_currency' => 0.0,
            'balance'         => 0.0,
            'tax_line_id'     => null,
            'tax_group_id'    => null,
        ];

        $pending[$groupingKey]['name'] = $taxData['tax']->name;
        $pending[$groupingKey]['tax_base_amount'] += $baseLine['sign'] * $taxData['base_amount'] * ($baseLine['tax_tag_invert'] ? -1 : 1);
        $pending[$groupingKey]['amount_currency'] += $baseLine['sign'] * $repartitionData['tax_amount_currency'];
        $pending[$groupingKey]['balance'] += $baseLine['sign'] * $repartitionData['tax_amount'];
        $pending[$groupingKey]['tax_line_id'] = $repartitionData['tax_rep']->tax_id;
        $pending[$groupingKey]['tax_group_id'] = $repartitionData['tax_rep']->tax->tax_group_id;
    }

    protected function isMaterial(array $entry, $company): bool
    {
        $currencyId = $entry['grouping_key']['currency_id'] ?? null;

        if ($currencyId && ! Currency::find($currencyId)->isZero($entry['amount_currency'])) {
            return true;
        }

        return ! $company->currency->isZero($entry['balance']);
    }

    protected function matchExistingTaxLines(array $taxLines, array $pending): array
    {
        $toUpdate = [];

        $toDelete = [];

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

            if (! isset($pending[$groupingKey])) {
                $toDelete[] = $taxLine;

                continue;
            }

            $amounts = $pending[$groupingKey];

            unset($pending[$groupingKey]);

            $toUpdate[] = array_merge($taxLine, $amounts['grouping_key'], $amounts);
        }

        return [$toUpdate, $toDelete, $pending];
    }
}
