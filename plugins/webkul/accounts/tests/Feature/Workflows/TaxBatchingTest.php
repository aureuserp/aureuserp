<?php

use Webkul\Account\Enums\AmountType;
use Webkul\Account\Enums\TaxIncludeOverride;
use Webkul\Account\Enums\TypeTaxUse;
use Webkul\Account\Models\Tax;
use Webkul\Account\Services\TaxComputer;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../Helpers/AccountHelper.php';

beforeEach(fn () => TestBootstrapHelper::ensurePluginInstalled('accounts'));

/**
 * The factory leaves every tax on sort 0, so the order is set explicitly here.
 * Attributes are written through the query builder to skip the repartition
 * validation of the saved hook, which the helper fixtures do not satisfy.
 */
function batchedTax(
    float $amount,
    AmountType $amountType = AmountType::PERCENT,
    TaxIncludeOverride $include = TaxIncludeOverride::TAX_EXCLUDED,
    int $sort = 0,
    array $flags = [],
): Tax {
    $tax = AccountHelper::taxWithAccounts($amount, $amountType, TypeTaxUse::SALE, $include);

    Tax::query()->whereKey($tax->id)->update(['sort' => $sort] + $flags);

    return $tax->refresh();
}

function computeFor(array $taxes, float $priceUnit, float $quantity = 1.0): array
{
    $result = app(TaxComputer::class)->computeTaxes(
        taxes: Tax::query()
            ->whereIn('id', collect($taxes)->pluck('id')->all())
            ->orderBy('sort')
            ->orderBy('id')
            ->get(),
        priceUnit: $priceUnit,
        quantity: $quantity,
    );

    return [
        'total_excluded' => round($result['total_excluded'], 2),
        'total_included' => round($result['total_included'], 2),
        'tax_amounts'    => collect($result['taxes_data'])->map(fn ($d) => round($d['tax_amount'], 2))->values()->all(),
        'base_amounts'   => collect($result['taxes_data'])->map(fn ($d) => round($d['base_amount'], 2))->values()->all(),
    ];
}

it('batches price-included percent taxes so the base excludes both of them at once', function () {
    // 10% + 5% price included on 115 -> base is exactly 100, not 115 minus two
    // separately computed amounts.
    $ten = batchedTax(10, AmountType::PERCENT, TaxIncludeOverride::TAX_INCLUDED, sort: 1);
    $five = batchedTax(5, AmountType::PERCENT, TaxIncludeOverride::TAX_INCLUDED, sort: 2);

    expect(computeFor([$ten, $five], 115.0))->toBe([
        'total_excluded' => 100.0,
        'total_included' => 115.0,
        'tax_amounts'    => [10.0, 5.0],
        'base_amounts'   => [100.0, 100.0],
    ]);
});

it('splits CGST and SGST equally on a price-included line', function () {
    // The case that matters in India: 9 + 9 on an MRP of 118 must be 9 and 9,
    // never 8.94 and 9.74.
    $cgst = batchedTax(9, AmountType::PERCENT, TaxIncludeOverride::TAX_INCLUDED, sort: 1);
    $sgst = batchedTax(9, AmountType::PERCENT, TaxIncludeOverride::TAX_INCLUDED, sort: 2);

    expect(computeFor([$cgst, $sgst], 118.0))->toBe([
        'total_excluded' => 100.0,
        'total_included' => 118.0,
        'tax_amounts'    => [9.0, 9.0],
        'base_amounts'   => [100.0, 100.0],
    ]);
});

it('batches division taxes so each one is a share of the same total', function () {
    // Division means "percentage of the tax-included price", so 10% + 5% must
    // together be 15% of the total: 17.64 / 117.64.
    $ten = batchedTax(10, AmountType::DIVISION, TaxIncludeOverride::TAX_EXCLUDED, sort: 1);
    $five = batchedTax(5, AmountType::DIVISION, TaxIncludeOverride::TAX_EXCLUDED, sort: 2);

    expect(computeFor([$ten, $five], 100.0))->toBe([
        'total_excluded' => 100.0,
        'total_included' => 117.64,
        'tax_amounts'    => [11.76, 5.88],
        'base_amounts'   => [100.0, 100.0],
    ]);
});

it('keeps a single price-included tax unchanged', function () {
    $tax = batchedTax(21, AmountType::PERCENT, TaxIncludeOverride::TAX_INCLUDED, sort: 1);

    expect(computeFor([$tax], 121.0))->toBe([
        'total_excluded' => 100.0,
        'total_included' => 121.0,
        'tax_amounts'    => [21.0],
        'base_amounts'   => [100.0],
    ]);
});

it('keeps price-excluded percent taxes unchanged', function () {
    $ten = batchedTax(10, AmountType::PERCENT, TaxIncludeOverride::TAX_EXCLUDED, sort: 1);
    $five = batchedTax(5, AmountType::PERCENT, TaxIncludeOverride::TAX_EXCLUDED, sort: 2);

    expect(computeFor([$ten, $five], 100.0))->toBe([
        'total_excluded' => 100.0,
        'total_included' => 115.0,
        'tax_amounts'    => [10.0, 5.0],
        'base_amounts'   => [100.0, 100.0],
    ]);
});

it('does not batch taxes of different computation types', function () {
    $fixed = batchedTax(5, AmountType::FIXED, TaxIncludeOverride::TAX_EXCLUDED, sort: 1);
    $percent = batchedTax(10, AmountType::PERCENT, TaxIncludeOverride::TAX_EXCLUDED, sort: 2);

    expect(computeFor([$fixed, $percent], 100.0))->toBe([
        'total_excluded' => 100.0,
        'total_included' => 115.0,
        'tax_amounts'    => [5.0, 10.0],
        'base_amounts'   => [100.0, 100.0],
    ]);
});

it('does not batch across include_base_amount and keeps the chain working', function () {
    // The first tax feeds its amount into the base of the second one, so they
    // cannot share a batch: 10 on 100, then 5% on 110.
    $first = batchedTax(10, AmountType::PERCENT, TaxIncludeOverride::TAX_EXCLUDED, sort: 1, flags: [
        'include_base_amount' => true,
    ]);

    $second = batchedTax(5, AmountType::PERCENT, TaxIncludeOverride::TAX_EXCLUDED, sort: 2, flags: [
        'is_base_affected' => true,
    ]);

    expect(computeFor([$first, $second], 100.0))->toBe([
        'total_excluded' => 100.0,
        'total_included' => 115.5,
        'tax_amounts'    => [10.0, 5.5],
        'base_amounts'   => [100.0, 110.0],
    ]);
});
