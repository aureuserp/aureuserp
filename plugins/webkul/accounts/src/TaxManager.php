<?php

namespace Webkul\Account;

use Webkul\Account\Services\TaxAccountingMapper;
use Webkul\Account\Services\TaxComputer;
use Webkul\Account\Services\TaxDetailRounder;
use Webkul\Account\Services\TaxLineFactory;

class TaxManager
{
    public function __construct(
        protected TaxLineFactory $lineFactory,
        protected TaxComputer $computer,
        protected TaxDetailRounder $rounder,
        protected TaxAccountingMapper $mapper,
    ) {}

    public function makeBaseLine(
        mixed $record = null,
        mixed $product = null,
        mixed $taxes = null,
        ?float $priceUnit = null,
        ?float $quantity = null,
        ?float $discount = null,
        mixed $currency = null,
        string|bool $specialMode = false,
        string|bool $specialType = false,
        ?float $rate = null,
        ?array $manualTaxAmounts = null,
        ?float $sign = null,
        ?bool $isRefund = null,
        ?bool $taxTagInvert = null,
        mixed $partner = null,
        mixed $account = null,
        mixed $analyticDistribution = null,
    ): array {
        return $this->lineFactory->makeBaseLine(
            record: $record,
            product: $product,
            taxes: $taxes,
            priceUnit: $priceUnit,
            quantity: $quantity,
            discount: $discount,
            currency: $currency,
            specialMode: $specialMode,
            specialType: $specialType,
            rate: $rate,
            manualTaxAmounts: $manualTaxAmounts,
            sign: $sign,
            isRefund: $isRefund,
            taxTagInvert: $taxTagInvert,
            partner: $partner,
            account: $account,
            analyticDistribution: $analyticDistribution,
        );
    }

    public function makeTaxLine(
        mixed $record = null,
        ?float $sign = null,
        mixed $currency = null,
        mixed $partner = null,
        mixed $account = null,
    ): array {
        return $this->lineFactory->makeTaxLine($record, $sign, $currency, $partner, $account);
    }

    public function withTaxDetails(array $baseLine, $company, ?string $roundingMethod = null): array
    {
        return $this->computer->withTaxDetails($baseLine, $company, $roundingMethod);
    }

    public function addTaxDetailsToAll(array $baseLines, $company): array
    {
        return $this->computer->addTaxDetailsToAll($baseLines, $company);
    }

    public function withAccountingData(array $baseLine, $company, bool $useRawAmounts = false): array
    {
        return $this->mapper->withAccountingData($baseLine, $company, $useRawAmounts);
    }

    public function addAccountingDataToAll(array $baseLines, $company, bool $useRawAmounts = false): array
    {
        return array_map(
            fn (array $baseLine) => $this->mapper->withAccountingData($baseLine, $company, $useRawAmounts),
            $baseLines
        );
    }

    public function roundTaxDetails(array $baseLines, $company, array $taxLines = []): array
    {
        return $this->rounder->roundTaxDetails($baseLines, $company, $taxLines);
    }

    public function buildTaxLineChanges(array $baseLines, $company, array $taxLines = []): array
    {
        return $this->mapper->buildTaxLineChanges($baseLines, $company, $taxLines);
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
        return $this->computer->computeTaxes(
            $taxes,
            $priceUnit,
            $quantity,
            $precisionRounding,
            $roundingMethod,
            $product,
            $specialMode,
            $manualTaxAmounts
        );
    }

    public function computeAll(
        mixed $taxes,
        float $priceUnit,
        $currency = null,
        float $quantity = 1,
        $product = null,
        $partner = null,
        bool $isRefund = false,
        bool $handlePriceInclude = true,
        bool $forcePriceInclude = false,
        ?string $roundingMethod = null,
        bool $roundBase = false
    ): array {
        $company = $taxes->isEmpty() ? current_company() : $taxes->first()->company;

        $currency = $currency ?: $company->currency;

        $specialMode = match (true) {
            $forcePriceInclude    => 'total_included',
            ! $handlePriceInclude => 'total_excluded',
            default               => false,
        };

        $baseLine = $this->makeBaseLine(
            partner: $partner,
            currency: $currency,
            product: $product,
            taxes: $taxes,
            priceUnit: $priceUnit,
            quantity: $quantity,
            isRefund: $isRefund,
            specialMode: $specialMode,
        );

        $baseLine = $this->withTaxDetails($baseLine, $company, roundingMethod: $roundingMethod);

        $baseLine = $this->addAccountingDataToAll([$baseLine], $company, useRawAmounts: true)[0];

        $taxDetails = $baseLine['tax_details'];

        $totalVoid = $totalExcluded = $taxDetails['raw_total_excluded_currency'];

        $totalIncluded = $taxDetails['raw_total_included_currency'];

        $breakdown = [];

        foreach ($taxDetails['taxes_data'] as $taxData) {
            $tax = $taxData['tax'];

            foreach ($taxData['tax_reps_data'] as $repartitionData) {
                $repartition = $repartitionData['tax_rep'];

                $breakdown[] = [
                    'id'                      => $tax->id,
                    'name'                    => $tax->name,
                    'amount'                  => $repartitionData['tax_amount_currency'],
                    'base'                    => $taxData['raw_base_amount_currency'],
                    'sort'                    => $tax->sort,
                    'account_id'              => $repartitionData['account']->id,
                    'analytic'                => $tax->analytic,
                    'price_include'           => $tax->price_include,
                    'tax_exigibility'         => $tax->tax_exigibility,
                    'tax_repartition_line_id' => $repartition->id,
                    'group'                   => $taxData['group'],
                    'tax_ids'                 => $repartitionData['taxes']->pluck('id')->toArray(),
                ];

                if (! $repartition->account_id) {
                    $totalVoid += $repartitionData['tax_amount_currency'];
                }
            }
        }

        return [
            'taxes'          => $breakdown,
            'total_excluded' => $roundBase ? $currency->round($totalExcluded) : $totalExcluded,
            'total_included' => $roundBase ? $currency->round($totalIncluded) : $totalIncluded,
            'total_void'     => $totalVoid,
        ];
    }

    public function priceWithoutIncludedTaxes(float $price, $productTaxes, $lineTaxes): float
    {
        $includedTaxes = $productTaxes->filter(fn ($tax) => ! $lineTaxes->contains($tax) && $tax->price_include);

        if ($includedTaxes->isEmpty()) {
            return $price;
        }

        return $this->computeAll($includedTaxes, $price)['total_excluded'];
    }

    public function priceWithoutIncludedTaxesForCompany(float $price, $productTaxes, $lineTaxes, $company): float
    {
        return $this->priceWithoutIncludedTaxes(
            $price,
            $productTaxes->filter(fn ($tax) => $tax->company_id === $company->id),
            $lineTaxes->filter(fn ($tax) => $tax->company_id === $company->id)
        );
    }
}
