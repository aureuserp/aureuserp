<?php

namespace Webkul\PointOfSale\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Webkul\Account\Models\Product as AccountProduct;
use Webkul\Inventory\Enums\ProductTracking;
use Webkul\PointOfSale\Models\Category;
use Webkul\PointOfSale\Models\Config;
use Webkul\Product\Enums\ProductType;
use Webkul\Product\Models\Category as ProductCategory;
use Webkul\Product\Models\Product;
use Webkul\Support\Models\UOM;

class TerminalProductCreator
{
    public function create(Config $config, array $data): Product
    {
        $name = trim((string) ($data['name'] ?? ''));

        if ($name === '') {
            throw ValidationException::withMessages([
                'newProduct.name' => __('point-of-sale::system.terminal-product-creator.name-required'),
            ]);
        }

        return DB::transaction(function () use ($config, $data, $name): Product {
            $uomId = UOM::query()->orderBy('id')->value('id');

            $categoryId = ProductCategory::query()->orderBy('id')->value('id');

            if (! $uomId || ! $categoryId) {
                throw ValidationException::withMessages([
                    'newProduct.name' => __('point-of-sale::system.terminal-product-creator.defaults-missing'),
                ]);
            }

            $product = Product::create([
                'type'             => ProductType::GOODS,
                'name'             => $name,
                'barcode'          => $data['barcode'] ?? null,
                'price'            => (float) ($data['price'] ?? 0),
                'is_storable'      => (bool) ($data['is_storable'] ?? false),
                'tracking'         => ($data['is_storable'] ?? false)
                    ? ($data['tracking'] ?? ProductTracking::QTY->value)
                    : ProductTracking::QTY->value,
                'uom_id'           => $uomId,
                'uom_po_id'        => $uomId,
                'category_id'      => $categoryId,
                'available_in_pos' => true,
                'company_id'       => $config->company_id,
            ]);

            $taxIds = array_filter(array_map('intval', $data['tax_ids'] ?? []));

            if ($taxIds) {
                AccountProduct::withoutGlobalScopes()
                    ->find($product->id)
                    ?->productTaxes()
                    ->sync($taxIds);
            }

            $posCategoryId = $data['category_id'] ?? null;

            if ($posCategoryId && Category::query()->whereKey($posCategoryId)->exists()) {
                $product->posCategories()->sync([$posCategoryId]);
            }

            return $product;
        });
    }
}
