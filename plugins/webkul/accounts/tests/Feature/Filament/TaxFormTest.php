<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Webkul\Account\Enums\AmountType;
use Webkul\Account\Enums\TypeTaxUse;
use Webkul\Account\Filament\Resources\TaxResource\Pages\CreateTax;
use Webkul\Account\Models\Tax;
use Webkul\Account\Models\TaxGroup;
use Webkul\Account\Services\TaxComputer;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Package;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/FilamentHelper.php';
require_once __DIR__.'/../../Helpers/AccountHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('accounts');

    DB::table('plugins')->where('name', 'accounts')->update([
        'is_installed' => true,
        'is_active'    => true,
        'updated_at'   => now(),
    ]);

    Package::$plugins = Plugin::all()->keyBy('name');

    URL::resolveMissingNamedRoutesUsing(fn () => '#');

    FilamentHelper::actingAs(['create_account_tax', 'view_any_account_tax']);
});

function taxForm(string $amountType)
{
    return Livewire::test(CreateTax::class)
        ->fillForm(['amount_type' => $amountType]);
}

it('asks for an amount on percentage, division and fixed taxes', function (string $amountType) {
    taxForm($amountType)
        ->assertFormFieldVisible('amount')
        ->assertFormFieldHidden('formula')
        ->assertFormFieldHidden('childrenTaxes');
})->with([
    AmountType::PERCENT->value,
    AmountType::DIVISION->value,
    AmountType::FIXED->value,
]);

it('swaps the amount for a formula on custom formula taxes', function () {
    taxForm(AmountType::CODE->value)
        ->assertFormFieldHidden('amount')
        ->assertFormFieldVisible('formula')
        ->assertFormFieldHidden('childrenTaxes');
});

it('swaps the amount for children taxes on group taxes', function () {
    taxForm(AmountType::GROUP->value)
        ->assertFormFieldHidden('amount')
        ->assertFormFieldHidden('formula')
        ->assertFormFieldVisible('childrenTaxes');
});

it('refuses to save a formula that is not plain arithmetic', function () {
    $taxGroup = TaxGroup::query()->firstOrCreate(['name' => 'Formula Test Group']);

    Livewire::test(CreateTax::class)
        ->fillForm([
            'name'         => 'Broken formula',
            'type_tax_use' => TypeTaxUse::SALE->value,
            'amount_type'  => AmountType::CODE->value,
            'formula'      => 'exec("ls")',
            'tax_group_id' => $taxGroup->id,
        ])
        ->call('create')
        ->assertHasFormErrors(['formula']);

    expect(Tax::query()->where('name', 'Broken formula')->exists())->toBeFalse();
});

it('saves a valid formula tax', function () {
    $taxGroup = TaxGroup::query()->firstOrCreate(['name' => 'Formula Test Group']);

    $account = AccountHelper::account('income');

    $lines = fn (string $documentType) => [
        ['document_type' => $documentType, 'repartition_type' => 'base', 'factor_percent' => null, 'account_id' => null],
        ['document_type' => $documentType, 'repartition_type' => 'tax', 'factor_percent' => 100, 'account_id' => $account->id],
    ];

    Livewire::test(CreateTax::class)
        ->fillForm([
            'name'                      => 'GST 18%',
            'type_tax_use'              => TypeTaxUse::SALE->value,
            'amount_type'               => AmountType::CODE->value,
            'formula'                   => 'price_subtotal * 0.18',
            'tax_group_id'              => $taxGroup->id,
            'invoiceRepartitionLines'   => $lines('invoice'),
            'refundRepartitionLines'    => $lines('refund'),
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    expect(Tax::query()->where('name', 'GST 18%')->value('formula'))->toBe('price_subtotal * 0.18');
});

it('drops the children when a tax stops being a group', function () {
    $group = AccountHelper::groupTax([AccountHelper::tax(10)]);

    expect($group->childrenTaxes()->count())->toBe(1);

    $group->update(['amount_type' => AmountType::PERCENT->value, 'amount' => 10]);

    expect($group->childrenTaxes()->count())->toBe(0);
});

it('computes the tax amount from the formula', function () {
    $tax = AccountHelper::taxWithAccounts(0, AmountType::CODE);

    // Updated through the query builder so the repartition validation of the
    // saved hook, which the helper's fixtures do not satisfy, stays out of the way.
    Tax::query()->whereKey($tax->id)->update(['formula' => 'price_subtotal * 0.18']);

    $computation = app(TaxComputer::class)->computeTaxes(
        taxes: Tax::query()->whereKey($tax->id)->get(),
        priceUnit: 100.0,
        quantity: 3.0,
    );

    expect($computation['taxes_data'][0]['tax_amount'])->toBe(54.0)
        ->and($computation['total_included'])->toBe(354.0);
});

it('caps the computed amount when the formula uses min', function () {
    $tax = AccountHelper::taxWithAccounts(0, AmountType::CODE);

    Tax::query()->whereKey($tax->id)->update(['formula' => 'min(price_subtotal * 0.18, 20)']);

    $computation = app(TaxComputer::class)->computeTaxes(
        taxes: Tax::query()->whereKey($tax->id)->get(),
        priceUnit: 100.0,
        quantity: 3.0,
    );

    expect($computation['taxes_data'][0]['tax_amount'])->toBe(20.0)
        ->and($computation['total_included'])->toBe(320.0);
});
