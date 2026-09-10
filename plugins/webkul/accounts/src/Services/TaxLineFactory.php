<?php

namespace Webkul\Account\Services;

use Illuminate\Database\Eloquent\Model;
use Webkul\Account\Models\Account;
use Webkul\Account\Models\Product;
use Webkul\Account\Models\Tax;
use Webkul\Account\Models\TaxPartition;
use Webkul\Partner\Models\Partner;
use Webkul\Support\Models\Currency;

class TaxLineFactory
{
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
        return [
            'record' => $record,
            'id'     => $this->fromRecord($record, 'id', 0),

            'product'    => $product ?? $this->fromRecord($record, 'product', new Product),
            'taxes'      => $taxes ?? $this->fromRecord($record, 'taxes', new Tax),
            'price_unit' => $priceUnit ?? $this->fromRecord($record, 'price_unit', 0.0),
            'quantity'   => $quantity ?? $this->fromRecord($record, 'quantity', 0.0),
            'discount'   => $discount ?? $this->fromRecord($record, 'discount', 0.0),
            'currency'   => $this->resolveCurrency($record, $currency),

            'special_mode' => $specialMode,
            'special_type' => $specialType,

            'rate' => $rate ?? $this->fromRecord($record, 'rate', 1.0),

            'manual_tax_amounts' => $manualTaxAmounts,

            'sign'           => $sign ?? $this->fromRecord($record, 'sign', 1.0),
            'is_refund'      => $isRefund ?? $this->fromRecord($record, 'is_refund', false),
            'tax_tag_invert' => $taxTagInvert ?? $this->fromRecord($record, 'tax_tag_invert', false),

            'partner'               => $partner ?? $this->fromRecord($record, 'partner', new Partner),
            'account'               => $account ?? $this->fromRecord($record, 'account', new Account),
            'analytic_distribution' => $analyticDistribution ?? $this->fromRecord($record, 'analytic_distribution', null),
        ];
    }

    public function makeTaxLine(
        mixed $record = null,
        ?float $sign = null,
        mixed $currency = null,
        mixed $partner = null,
        mixed $account = null,
    ): array {
        return [
            'record' => $record,
            'id'     => $this->fromRecord($record, 'id', 0),

            'taxRepartitionLine'    => $this->fromRecord($record, 'taxRepartitionLine', new TaxPartition),
            'groupTax'              => $this->fromRecord($record, 'groupTax', new Tax),
            'taxes'                 => $this->fromRecord($record, 'taxes', new Tax),
            'tax_tags'              => $this->fromRecord($record, 'tax_tags', []),
            'currency'              => $this->resolveCurrency($record, $currency),
            'partner'               => $partner ?? $this->fromRecord($record, 'partner', new Partner),
            'account'               => $account ?? $this->fromRecord($record, 'account', new Account),
            'analytic_distribution' => $this->fromRecord($record, 'analytic_distribution', null),
            'sign'                  => $sign ?? $this->fromRecord($record, 'sign', 1.0),
            'amount_currency'       => $this->fromRecord($record, 'amount_currency', 0),
            'balance'               => $this->fromRecord($record, 'balance', 0),
        ];
    }

    public function fromRecord(mixed $record, string $field, mixed $fallback = null): mixed
    {
        $value = match (true) {
            $record instanceof Model && array_key_exists($field, $record->getAttributes()) => $record->getAttribute($field) ?? $fallback,
            $record instanceof Model && method_exists($record, $field)                     => $record->{$field} ?? $fallback,
            is_array($record)                                                              => $record[$field] ?? $fallback,
            default                                                                        => $fallback,
        };

        if ($fallback instanceof Model && isset($value->_origin)) {
            return $value->_origin;
        }

        return $value;
    }

    protected function resolveCurrency(mixed $record, mixed $currency): mixed
    {
        return $currency
            ?: $this->fromRecord($record, 'currency', null)
            ?: $this->fromRecord($record, 'companyCurrency', null)
            ?: $this->fromRecord($record, 'company', current_company())?->currency
            ?: new Currency;
    }
}
