<?php

use Filament\Actions\Testing\TestAction;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Webkul\Support\Filament\Resources\CompanyResource\Pages\CreateCompany;
use Webkul\Support\Filament\Resources\CompanyResource\Pages\EditCompany;
use Webkul\Support\Filament\Resources\CompanyResource\RelationManagers\BranchesRelationManager;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Currency;

require_once __DIR__.'/../../Helpers/FilamentHelper.php';
require_once __DIR__.'/../../Helpers/TestBootstrapHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensureERPInstalled();

    SecurityHelper::disableUserEvents();

    URL::resolveMissingNamedRoutesUsing(fn () => '#');
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

function companyCurrencyOptionData(array $overrides = []): array
{
    return [
        'name'           => 'TC'.random_int(1000, 9999),
        'full_name'      => 'Test Currency '.uniqid(),
        'symbol'         => 'T$',
        'iso_numeric'    => 999,
        'decimal_places' => 2,
        'rounding'       => 0.01,
        'active'         => true,
        ...$overrides,
    ];
}

function companyCurrencyOptionAction(): TestAction
{
    return TestAction::make('createOption')->schemaComponent('currency_id');
}

it('creates a currency from the company form', function () {
    FilamentHelper::actingAs(['view_any_support_company', 'create_support_company', 'create_support_currency']);

    $data = companyCurrencyOptionData(['rounding' => 0.05]);

    Livewire::test(CreateCompany::class)
        ->callAction(companyCurrencyOptionAction(), data: $data)
        ->assertHasNoActionErrors();

    expect((float) Currency::where('name', $data['name'])->value('rounding'))->toBe(0.05);
});

it('rejects an invalid currency from the company form', function (array $overrides, string $field, string $rule) {
    FilamentHelper::actingAs(['view_any_support_company', 'create_support_company', 'create_support_currency']);

    $data = companyCurrencyOptionData($overrides);

    Livewire::test(CreateCompany::class)
        ->callAction(companyCurrencyOptionAction(), data: $data)
        ->assertHasActionErrors([$field => $rule]);

    expect(Currency::where('name', $data['name'])->exists())->toBeFalse();
})->with([
    'zero rounding'           => [['rounding' => 0], 'rounding', 'min'],
    'empty rounding'          => [['rounding' => null], 'rounding', 'required'],
    'too large rounding'      => [['rounding' => 1000000], 'rounding', 'max'],
    'too many decimal places' => [['decimal_places' => 11], 'decimal_places', 'max'],
    'fractional places'       => [['decimal_places' => 1.5], 'decimal_places', 'integer'],
]);

it('rejects a zero rounding from the branch form', function () {
    FilamentHelper::actingAs([
        'view_any_support_company',
        'view_support_company',
        'update_support_company',
        'create_support_company',
        'create_support_currency',
    ]);

    $company = Company::query()->whereNull('parent_id')->firstOrFail();

    $data = companyCurrencyOptionData(['rounding' => 0]);

    Livewire::test(BranchesRelationManager::class, ['ownerRecord' => $company, 'pageClass' => EditCompany::class])
        ->callAction([TestAction::make('create')->table(), companyCurrencyOptionAction()], data: $data)
        ->assertHasActionErrors(['rounding' => 'min']);

    expect(Currency::where('name', $data['name'])->exists())->toBeFalse();
});
