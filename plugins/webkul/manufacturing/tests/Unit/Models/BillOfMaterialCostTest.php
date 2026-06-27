<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Collection;
use Webkul\Manufacturing\Models\BillOfMaterial;
use Webkul\Manufacturing\Models\BillOfMaterialLine;
use Webkul\Product\Models\Product;
use Webkul\Support\Models\UOM;

function manufacturingCostUom(int $id, string $name, float $factor, int $categoryId = 2): UOM
{
    $uom = new UOM([
        'name'        => $name,
        'factor'      => $factor,
        'rounding'    => 0.01,
        'category_id' => $categoryId,
    ]);
    $uom->id = $id;

    return $uom;
}

function manufacturingCostProduct(float $cost, UOM $uom, UOM $costUom): Product
{
    $product = new Product([
        'cost'      => $cost,
        'uom_id'    => $uom->id,
        'uom_po_id' => $costUom->id,
    ]);
    $product->setRelation('uom', $uom);
    $product->setRelation('uomPO', $costUom);

    return $product;
}

function manufacturingCostBom(Product $product, UOM $lineUom, float $lineQuantity): BillOfMaterial
{
    $line = new BillOfMaterialLine([
        'quantity' => $lineQuantity,
        'uom_id'   => $lineUom->id,
    ]);
    $line->setRelation('product', $product);
    $line->setRelation('uom', $lineUom);
    $line->setRelation('attributeValues', new Collection);

    $billOfMaterial = new BillOfMaterial(['quantity' => 1]);
    $billOfMaterial->setRelation('lines', new Collection([$line]));
    $billOfMaterial->setRelation('operations', new Collection);

    return $billOfMaterial;
}

it('calculates the same physical quantity cost in grams or kilograms', function () {
    $kilogram = manufacturingCostUom(1, 'kg', 1);
    $gram = manufacturingCostUom(2, 'g', 1000);
    $product = manufacturingCostProduct(100, $kilogram, $kilogram);

    expect($product->getCostForQuantity(0.05, $kilogram))->toBe(5.0)
        ->and($product->getCostForQuantity(50, $gram))->toBe(5.0);
});

it('uses the current product cost unit without rewriting an existing bom line', function () {
    $kilogram = manufacturingCostUom(1, 'kg', 1);
    $gram = manufacturingCostUom(2, 'g', 1000);
    $product = manufacturingCostProduct(100, $kilogram, $kilogram);
    $billOfMaterial = manufacturingCostBom($product, $kilogram, 0.05);

    expect($billOfMaterial->getUnitComponentCost())->toBe(5.0);

    $product->cost = 0.10;
    $product->uom_po_id = $gram->id;
    $product->setRelation('uomPO', $gram);

    expect($billOfMaterial->getUnitComponentCost())->toBe(5.0);
});

it('converts component quantities before applying the bom quantity multiplier', function () {
    $kilogram = manufacturingCostUom(1, 'kg', 1);
    $gram = manufacturingCostUom(2, 'g', 1000);
    $product = manufacturingCostProduct(0.10, $gram, $gram);
    $billOfMaterial = manufacturingCostBom($product, $kilogram, 0.05);

    expect($billOfMaterial->getComponentCost(3))->toBe(15.0);
});

it('rejects cost conversion between unrelated uom categories', function () {
    $kilogram = manufacturingCostUom(1, 'kg', 1);
    $unit = manufacturingCostUom(2, 'Units', 1, categoryId: 1);
    $product = manufacturingCostProduct(10, $unit, $unit);

    expect(fn (): float => $product->getCostForQuantity(1, $kilogram))
        ->toThrow(Exception::class);
});
