<?php

use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Webkul\Support\Filament\Resources\CurrencyResource\Pages\CreateCurrency;
use Webkul\Support\Filament\Resources\CurrencyResource\Pages\EditCurrency;
use Webkul\Support\Models\Currency;

require_once __DIR__.'/../../Helpers/FilamentHelper.php';
require_once __DIR__.'/../../Helpers/TestBootstrapHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensureERPInstalled();

    SecurityHelper::disableUserEvents();

    URL::resolveMissingNamedRoutesUsing(fn () => '#');
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

function currencyFormData(array $overrides = []): array
{
    return [
        'name'           => 'TC'.random_int(1000, 9999),
        'full_name'      => 'Test Currency '.uniqid(),
        'symbol'         => 'T$',
        'decimal_places' => 2,
        'rounding'       => 0.01,
        'active'         => true,
        ...$overrides,
    ];
}

it('creates a currency with a positive rounding', function () {
    FilamentHelper::actingAs(['view_any_support_currency', 'create_support_currency']);

    $data = currencyFormData(['rounding' => 0.05]);

    Livewire::test(CreateCurrency::class)
        ->fillForm($data)
        ->call('create')
        ->assertHasNoFormErrors();

    expect((float) Currency::where('name', $data['name'])->value('rounding'))->toBe(0.05);
});

it('rejects a currency rounding outside the allowed range on create', function (mixed $rounding, string $rule) {
    FilamentHelper::actingAs(['view_any_support_currency', 'create_support_currency']);

    Livewire::test(CreateCurrency::class)
        ->fillForm(currencyFormData(['rounding' => $rounding]))
        ->call('create')
        ->assertHasFormErrors(['rounding' => $rule]);
})->with([
    'zero'              => [0, 'min'],
    'negative'          => [-0.01, 'min'],
    'below the storage' => [0.0000001, 'min'],
    'too large'         => [1000000, 'max'],
    'empty'             => [null, 'required'],
]);

it('rejects clearing the rounding of an existing currency', function () {
    FilamentHelper::actingAs(['view_any_support_currency', 'view_support_currency', 'update_support_currency']);

    $currency = Currency::factory()->create();

    Livewire::test(EditCurrency::class, ['record' => $currency->getRouteKey()])
        ->fillForm(['rounding' => null])
        ->call('save')
        ->assertHasFormErrors(['rounding' => 'required']);

    expect((float) $currency->refresh()->rounding)->toBe(0.01);
});
