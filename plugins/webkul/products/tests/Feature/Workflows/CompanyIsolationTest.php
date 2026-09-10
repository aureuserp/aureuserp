<?php

use Webkul\Product\Models\Packaging;
use Webkul\Product\Models\PriceList;
use Webkul\Product\Models\Product;

require_once __DIR__.'/../../../../support/tests/Helpers/CompanyHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('products');
    SecurityHelper::disableUserEvents();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

it('hides price lists owned by another company', function () {
    $companyA = CompanyHelper::company();
    $companyB = CompanyHelper::company();

    $ownPriceList = PriceList::factory()->company($companyA)->create();
    $otherPriceList = PriceList::factory()->company($companyB)->create();

    CompanyHelper::actingAsCompanyUser($companyA);

    $visible = PriceList::query()->pluck('id');

    expect($visible)->toContain($ownPriceList->id)
        ->not->toContain($otherPriceList->id);
});

it('stamps a new price list with the active company', function () {
    $companyB = CompanyHelper::company();

    CompanyHelper::actingAsCompanyUser($companyB);

    $priceList = PriceList::factory()->create();

    expect($priceList->company_id)->toBe($companyB->id);
});

it('leaves product packaging shared even when its product belongs to a company', function () {
    $companyA = CompanyHelper::company();
    $companyB = CompanyHelper::company();

    CompanyHelper::actingAsCompanyUser($companyA);

    $product = Product::factory()->create(['company_id' => $companyA->id]);

    $packaging = Packaging::factory()->create(['product_id' => $product->id]);

    expect($packaging->company_id)->toBeNull()
        ->and(Packaging::query()->pluck('id'))->toContain($packaging->id);

    CompanyHelper::actingAsCompanyUser($companyB);

    expect(Packaging::query()->pluck('id'))->toContain($packaging->id);
});
