<?php

use Illuminate\Support\Collection;
use Webkul\Account\Enums\AmountType;
use Webkul\Account\Facades\Tax as TaxFacade;
use Webkul\Account\Models\Tax;
use Webkul\Account\Settings\TaxesSettings;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../inventories/tests/Helpers/InventoryHelper.php';
require_once __DIR__.'/../../Helpers/PosHelper.php';
require_once __DIR__.'/../../Helpers/TaxParityHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('inventories');
    TestBootstrapHelper::ensurePluginInstalled('accounts');
    TestBootstrapHelper::ensurePluginInstalled('point-of-sale');

    InventoryHelper::actingAsAdmin();

    $this->currency = TaxParityHelper::company()->currency;

    $this->roundingMethod = (new TaxesSettings)->tax_calculation_rounding_method;
});

function parityScenarios(string $roundingMethod, $currency): array
{
    $percent = TaxParityHelper::tax(['name' => 'Percent 10', 'amount' => 10.0, 'sort' => 1]);
    $percentLow = TaxParityHelper::tax(['name' => 'Percent 5', 'amount' => 5.0, 'sort' => 2]);
    $included = TaxParityHelper::included(20.0, ['name' => 'Included 20', 'sort' => 1]);
    $includedLow = TaxParityHelper::included(7.0, ['name' => 'Included 7', 'sort' => 2]);
    $fixed = TaxParityHelper::tax(['name' => 'Fixed 5', 'amount_type' => AmountType::FIXED, 'amount' => 5.0, 'sort' => 1]);
    $division = TaxParityHelper::tax(['name' => 'Division 10', 'amount_type' => AmountType::DIVISION, 'amount' => 10.0, 'sort' => 1]);
    $affecting = TaxParityHelper::tax(['name' => 'Affecting 10', 'amount' => 10.0, 'sort' => 1, 'include_base_amount' => true]);
    $affected = TaxParityHelper::tax(['name' => 'Affected 5', 'amount' => 5.0, 'sort' => 2, 'is_base_affected' => true]);
    $childA = TaxParityHelper::tax(['name' => 'Child 6', 'amount' => 6.0, 'sort' => 1]);
    $childB = TaxParityHelper::tax(['name' => 'Child 4', 'amount' => 4.0, 'sort' => 2]);
    $group = TaxParityHelper::group([$childA, $childB], ['name' => 'Group 10', 'sort' => 1]);
    $capped = TaxParityHelper::tax([
        'name'        => 'Capped code',
        'amount_type' => AmountType::CODE,
        'amount'      => 0.0,
        'formula'     => 'min(price_subtotal * 0.2, 15)',
        'sort'        => 1,
    ]);
    $negativeFactor = TaxParityHelper::tax(['name' => 'Reverse 21', 'amount' => 21.0, 'sort' => 1], taxFactorPercent: -100);

    $only = fn ($tax): Collection => collect([$tax]);

    $rounding = (float) $currency->rounding;

    $defaults = ['currency_rounding' => $rounding, 'rounding_method' => $roundingMethod];

    return [
        TaxParityHelper::scenario('percent excluded', $only($percent), $defaults),
        TaxParityHelper::scenario('percent excluded qty', $only($percent), $defaults + ['quantity' => 3.0, 'price_unit' => 33.33]),
        TaxParityHelper::scenario('percent excluded discount', $only($percent), $defaults + ['discount' => 15.0]),
        TaxParityHelper::scenario('percent excluded refund', $only($percent), $defaults + ['quantity' => -2.0]),
        TaxParityHelper::scenario('percent excluded negative price', $only($percent), $defaults + ['price_unit' => -49.99]),
        TaxParityHelper::scenario('two percents excluded', collect([$percent, $percentLow]), $defaults + ['price_unit' => 19.99, 'quantity' => 7.0]),
        TaxParityHelper::scenario('percent included', $only($included), $defaults + ['price_unit' => 119.99]),
        TaxParityHelper::scenario('percent included qty', $only($included), $defaults + ['price_unit' => 12.35, 'quantity' => 9.0]),
        TaxParityHelper::scenario('two included', collect([$included, $includedLow]), $defaults + ['price_unit' => 145.55]),
        TaxParityHelper::scenario('included and excluded', collect([$included, $percentLow]), $defaults + ['price_unit' => 87.77, 'quantity' => 4.0]),
        TaxParityHelper::scenario('fixed', $only($fixed), $defaults + ['quantity' => 3.0]),
        TaxParityHelper::scenario('fixed negative price', $only($fixed), $defaults + ['price_unit' => -20.0, 'quantity' => 2.0]),
        TaxParityHelper::scenario('fixed and percent', collect([$fixed, $percent]), $defaults + ['price_unit' => 41.67, 'quantity' => 3.0]),
        TaxParityHelper::scenario('division', $only($division), $defaults + ['price_unit' => 77.77]),
        TaxParityHelper::scenario('compound base affected', collect([$affecting, $affected]), $defaults + ['price_unit' => 123.45]),
        TaxParityHelper::scenario('compound base affected qty', collect([$affecting, $affected]), $defaults + ['price_unit' => 9.99, 'quantity' => 13.0]),
        TaxParityHelper::scenario('group', $only($group), $defaults + ['price_unit' => 66.66, 'quantity' => 3.0]),
        TaxParityHelper::scenario('group and percent', collect([$group, $percentLow]), $defaults + ['price_unit' => 21.21]),
        TaxParityHelper::scenario('code capped', $only($capped), $defaults + ['price_unit' => 200.0]),
        TaxParityHelper::scenario('code under cap', $only($capped), $defaults + ['price_unit' => 40.0]),
        TaxParityHelper::scenario('force price include', $only($percent), $defaults + ['price_unit' => 110.0, 'force_price_include' => true]),
        TaxParityHelper::scenario('handle price include off', $only($included), $defaults + ['price_unit' => 110.0, 'handle_price_include' => false]),
        TaxParityHelper::scenario('rounding stress', collect([$percent, $percentLow]), $defaults + ['price_unit' => 0.07, 'quantity' => 3.0]),
        TaxParityHelper::scenario('negative factor', $only($negativeFactor), $defaults + ['price_unit' => 250.0]),
    ];
}

function phpOutcome(array $scenario, $currency): array
{
    $taxes = Tax::query()
        ->whereIn('id', $scenario['root_tax_ids'])
        ->get()
        ->sortBy(fn ($tax) => array_search($tax->id, $scenario['root_tax_ids']))
        ->values();

    $priceUnit = $scenario['price_unit'] * (1 - ($scenario['discount'] / 100));

    $result = TaxFacade::computeAll(
        $taxes,
        $priceUnit,
        $currency,
        $scenario['quantity'],
        null,
        null,
        false,
        $scenario['handle_price_include'] ?? true,
        $scenario['force_price_include'] ?? false,
        $scenario['rounding_method'],
    );

    $perTax = [];

    foreach ($result['taxes'] as $entry) {
        $perTax[$entry['id']] = ($perTax[$entry['id']] ?? 0) + $entry['amount'];
    }

    return [
        'total_excluded' => $result['total_excluded'],
        'total_included' => $result['total_included'],
        'per_tax'        => $perTax,
    ];
}

it('computes identical totals in php and javascript for every tax shape', function () {
    $scenarios = parityScenarios($this->roundingMethod, $this->currency);

    $javascript = TaxParityHelper::runJavascript($scenarios);

    expect($javascript)->toHaveCount(count($scenarios));

    $tolerance = (float) $this->currency->rounding / 2;

    foreach ($scenarios as $index => $scenario) {
        $expected = phpOutcome($scenario, $this->currency);

        $actual = $javascript[$index];

        expect($actual['error'] ?? null)->toBeNull("scenario [{$scenario['name']}] threw in javascript");

        expect(abs($actual['total_excluded'] - $expected['total_excluded']))
            ->toBeLessThanOrEqual($tolerance, "total_excluded mismatch in [{$scenario['name']}]");

        expect(abs($actual['total_included'] - $expected['total_included']))
            ->toBeLessThanOrEqual($tolerance, "total_included mismatch in [{$scenario['name']}]");
    }
});

it('computes identical per-tax amounts in php and javascript', function () {
    $scenarios = collect(parityScenarios($this->roundingMethod, $this->currency))
        ->reject(fn (array $scenario): bool => $scenario['name'] === 'negative factor')
        ->values()
        ->all();

    $javascript = TaxParityHelper::runJavascript($scenarios);

    $tolerance = (float) $this->currency->rounding / 2;

    foreach ($scenarios as $index => $scenario) {
        $expected = phpOutcome($scenario, $this->currency);

        $actual = collect($javascript[$index]['taxes'])
            ->groupBy('id')
            ->map(fn (Collection $entries): float => (float) $entries->sum('amount'))
            ->all();

        foreach ($expected['per_tax'] as $taxId => $amount) {
            expect($actual)->toHaveKey((string) $taxId, message: "tax {$taxId} missing in javascript for [{$scenario['name']}]");

            expect(abs($actual[$taxId] - $amount))
                ->toBeLessThanOrEqual($tolerance, "tax {$taxId} amount mismatch in [{$scenario['name']}]");
        }
    }
});

it('rounds floats identically in php and javascript', function () {
    $cases = [
        [2.675, 0.01, 'HALF-UP'],
        [-2.675, 0.01, 'HALF-UP'],
        [0.145, 0.01, 'HALF-UP'],
        [1.005, 0.01, 'HALF-UP'],
        [12345.678, 0.05, 'HALF-UP'],
        [-0.005, 0.01, 'HALF-UP'],
        [2.5, 1.0, 'HALF-EVEN'],
        [3.5, 1.0, 'HALF-EVEN'],
        [2.4, 1.0, 'UP'],
        [-2.4, 1.0, 'DOWN'],
    ];

    $scenarios = array_map(fn (array $case): array => [
        'float_round' => ['value' => $case[0], 'precision_rounding' => $case[1], 'rounding_method' => $case[2]],
    ], $cases);

    $javascript = TaxParityHelper::runJavascript($scenarios);

    foreach ($cases as $index => $case) {
        $expected = float_round($case[0], precisionRounding: $case[1], roundingMethod: $case[2]);

        expect((float) $javascript[$index]['value'])
            ->toBe($expected, "float_round mismatch for {$case[0]} @ {$case[1]} {$case[2]}");
    }
});
