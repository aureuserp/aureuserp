<?php

use Illuminate\Support\Collection;
use Symfony\Component\Process\Process;
use Webkul\Account\Enums\AmountType;
use Webkul\Account\Enums\DocumentType;
use Webkul\Account\Enums\RepartitionType;
use Webkul\Account\Enums\TaxIncludeOverride;
use Webkul\Account\Models\Tax;
use Webkul\Account\Models\TaxPartition;
use Webkul\Support\Models\Company;

class TaxParityHelper
{
    public static function company(): Company
    {
        return Company::query()->firstOrFail();
    }

    public static function tax(array $overrides = [], int $taxFactorPercent = 100): Tax
    {
        $tax = Tax::factory()->create(array_merge([
            'amount_type' => AmountType::PERCENT,
            'amount'      => 10.0,
            'sort'        => 1,
            'company_id'  => static::company()->id,
        ], $overrides));

        foreach ([DocumentType::INVOICE, DocumentType::REFUND] as $documentType) {
            TaxPartition::create([
                'tax_id'           => $tax->id,
                'document_type'    => $documentType,
                'repartition_type' => RepartitionType::BASE,
                'factor_percent'   => 100,
                'company_id'       => static::company()->id,
            ]);

            TaxPartition::create([
                'tax_id'           => $tax->id,
                'document_type'    => $documentType,
                'repartition_type' => RepartitionType::TAX,
                'factor_percent'   => $taxFactorPercent,
                'company_id'       => static::company()->id,
            ]);
        }

        return $tax->refresh();
    }

    public static function group(array $children, array $overrides = []): Tax
    {
        $group = static::tax(array_merge([
            'amount_type' => AmountType::GROUP,
            'amount'      => 0.0,
        ], $overrides));

        $group->childrenTaxes()->sync(collect($children)->pluck('id')->all());

        return $group->refresh();
    }

    public static function included(float $amount = 10.0, array $overrides = []): Tax
    {
        return static::tax(array_merge([
            'amount'                 => $amount,
            'price_include_override' => TaxIncludeOverride::TAX_INCLUDED,
        ], $overrides));
    }

    /**
     * @param  Collection<int, Tax>  $taxes
     */
    public static function serialize(Collection $taxes): array
    {
        $flat = [];

        $collect = function (Tax $tax) use (&$flat, &$collect): void {
            if (isset($flat[$tax->id])) {
                return;
            }

            $children = $tax->amount_type === AmountType::GROUP
                ? $tax->childrenTaxes()->orderBy('sort')->orderBy('id')->get()
                : collect();

            $flat[$tax->id] = [
                'id'                  => $tax->id,
                'name'                => $tax->name,
                'sort'                => (int) $tax->sort,
                'amount'              => (float) $tax->amount,
                'amount_type'         => $tax->amount_type->value,
                'formula'             => $tax->formula,
                'price_include'       => (bool) $tax->price_include,
                'include_base_amount' => (bool) $tax->include_base_amount,
                'is_base_affected'    => (bool) $tax->is_base_affected,
                'has_negative_factor' => (bool) $tax->has_negative_factor,
                'children_tax_ids'    => $children->pluck('id')->map(fn ($id): int => (int) $id)->all(),
            ];

            $children->each($collect);
        };

        $taxes->each($collect);

        return array_values($flat);
    }

    public static function scenario(string $name, Collection $taxes, array $options = []): array
    {
        return array_merge([
            'name'              => $name,
            'taxes'             => static::serialize($taxes),
            'root_tax_ids'      => $taxes->pluck('id')->map(fn ($id): int => (int) $id)->all(),
            'price_unit'        => 100.0,
            'quantity'          => 1.0,
            'discount'          => 0.0,
            'currency_rounding' => (float) static::company()->currency->rounding,
            'rounding_method'   => 'round_per_line',
        ], $options);
    }

    public static function runJavascript(array $scenarios): array
    {
        $script = realpath(__DIR__.'/../../resources/js/till/tax/parity.mjs');

        if ($script === false) {
            throw new RuntimeException('parity.mjs not found');
        }

        $process = new Process([static::node(), $script], base_path());

        $process->setInput(json_encode(array_values($scenarios)));

        $process->run();

        if (! $process->isSuccessful()) {
            throw new RuntimeException('node failed: '.$process->getErrorOutput().$process->getOutput());
        }

        $decoded = json_decode($process->getOutput(), true);

        if (! is_array($decoded)) {
            throw new RuntimeException('node returned unreadable output: '.$process->getOutput());
        }

        return $decoded;
    }

    public static function node(): string
    {
        $candidates = array_filter([
            env('POS_PARITY_NODE'),
            ...glob(($_SERVER['HOME'] ?? '/root').'/.nvm/versions/node/*/bin/node') ?: [],
            '/usr/bin/node',
            '/usr/local/bin/node',
        ]);

        foreach ($candidates as $candidate) {
            if (is_executable($candidate)) {
                return $candidate;
            }
        }

        return 'node';
    }
}
