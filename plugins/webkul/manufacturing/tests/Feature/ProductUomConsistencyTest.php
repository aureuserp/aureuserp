<?php

declare(strict_types=1);

use Illuminate\Validation\ValidationException;
use Webkul\Manufacturing\Models\BillOfMaterial;
use Webkul\Manufacturing\Models\BillOfMaterialLine;
use Webkul\Product\Models\Product;
use Webkul\Support\Models\UOM;
use Webkul\Support\Models\UOMCategory;

require_once __DIR__.'/../../../support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('manufacturing');
});

it('prevents a product uom category change from invalidating existing bom lines', function () {
    $weightCategory = UOMCategory::factory()->create(['name' => 'Weight']);
    $unitCategory = UOMCategory::factory()->create(['name' => 'Unit']);
    $kilogram = UOM::factory()->reference()->create([
        'name'        => 'Kilogram',
        'category_id' => $weightCategory->id,
    ]);
    $unit = UOM::factory()->reference()->create([
        'name'        => 'Units',
        'category_id' => $unitCategory->id,
    ]);
    $product = Product::factory()->create([
        'uom_id'    => $kilogram->id,
        'uom_po_id' => $kilogram->id,
    ]);
    $billOfMaterial = BillOfMaterial::factory()->create();

    BillOfMaterialLine::query()->create([
        'bill_of_material_id' => $billOfMaterial->id,
        'product_id'          => $product->id,
        'uom_id'              => $kilogram->id,
        'quantity'            => 1,
    ]);

    $product->uom_id = $unit->id;
    $product->uom_po_id = $unit->id;

    expect(fn (): bool => $product->save())
        ->toThrow(ValidationException::class, 'cannot be changed');

    $product->refresh();

    expect($product->uom_id)->toBe($kilogram->id)
        ->and($product->uom_po_id)->toBe($kilogram->id);
});

it('allows compatible product uom changes when the product is used on a bom line', function () {
    $weightCategory = UOMCategory::factory()->create(['name' => 'Weight']);
    $kilogram = UOM::factory()->reference()->create([
        'name'        => 'Kilogram',
        'category_id' => $weightCategory->id,
    ]);
    $gram = UOM::factory()->create([
        'name'        => 'Gram',
        'factor'      => 1000,
        'category_id' => $weightCategory->id,
    ]);
    $product = Product::factory()->create([
        'uom_id'    => $kilogram->id,
        'uom_po_id' => $kilogram->id,
    ]);
    $billOfMaterial = BillOfMaterial::factory()->create();

    BillOfMaterialLine::query()->create([
        'bill_of_material_id' => $billOfMaterial->id,
        'product_id'          => $product->id,
        'uom_id'              => $kilogram->id,
        'quantity'            => 1,
    ]);

    $product->update([
        'uom_id'    => $gram->id,
        'uom_po_id' => $gram->id,
    ]);

    expect($product->refresh()->uom_id)->toBe($gram->id)
        ->and($product->uom_po_id)->toBe($gram->id);
});
