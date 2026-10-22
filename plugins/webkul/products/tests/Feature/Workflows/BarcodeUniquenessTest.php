<?php

use Illuminate\Support\Facades\Validator;
use Webkul\Product\Models\Packaging;
use Webkul\Product\Models\Product;
use Webkul\Product\Rules\UniqueBarcode;
use Webkul\Support\Models\Company;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('products');
});

function barcodeRulePasses(string $value, UniqueBarcode $rule): bool
{
    return Validator::make(['barcode' => $value], ['barcode' => [$rule]])->passes();
}

it('blocks a duplicate product barcode within the same company', function () {
    $company = Company::factory()->create();

    Product::factory()->company($company)->create(['barcode' => 'ABC123']);

    expect(barcodeRulePasses('ABC123', new UniqueBarcode($company->id)))->toBeFalse();
});

it('matches barcodes case-insensitively', function () {
    $company = Company::factory()->create();

    Product::factory()->company($company)->create(['barcode' => 'ABC123']);

    expect(barcodeRulePasses('abc123', new UniqueBarcode($company->id)))->toBeFalse();
});

it('allows the same barcode in a different company', function () {
    $companyA = Company::factory()->create();

    $companyB = Company::factory()->create();

    Product::factory()->company($companyA)->create(['barcode' => 'ABC123']);

    expect(barcodeRulePasses('ABC123', new UniqueBarcode($companyB->id)))->toBeTrue();
});

it('ignores the product being updated', function () {
    $company = Company::factory()->create();

    $product = Product::factory()->company($company)->create(['barcode' => 'ABC123']);

    expect(barcodeRulePasses('ABC123', new UniqueBarcode($company->id, $product)))->toBeTrue();
});

it('reserves the barcode of a soft-deleted product', function () {
    $company = Company::factory()->create();

    $product = Product::factory()->company($company)->create(['barcode' => 'ABC123']);

    $product->delete();

    expect(barcodeRulePasses('ABC123', new UniqueBarcode($company->id)))->toBeFalse();
});

it('blocks a packaging barcode that collides with a product barcode', function () {
    $company = Company::factory()->create();

    Product::factory()->company($company)->create(['barcode' => 'ABC123']);

    expect(barcodeRulePasses('ABC123', new UniqueBarcode($company->id, null, Packaging::class)))->toBeFalse();
});

it('blocks a product barcode that collides with a packaging barcode', function () {
    $company = Company::factory()->create();

    Packaging::factory()->company($company)->create(['barcode' => 'ABC123']);

    expect(barcodeRulePasses('ABC123', new UniqueBarcode($company->id)))->toBeFalse();
});

it('allows a blank barcode', function () {
    $company = Company::factory()->create();

    Product::factory()->company($company)->create(['barcode' => 'ABC123']);

    expect(barcodeRulePasses('', new UniqueBarcode($company->id)))->toBeTrue();
});
