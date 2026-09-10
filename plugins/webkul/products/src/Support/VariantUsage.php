<?php

namespace Webkul\Product\Support;

use Illuminate\Support\Collection;
use Webkul\Product\Models\Product;
use Webkul\Product\Models\ProductAttributeValue;
use Webkul\Product\Models\ProductCombination;
use Webkul\Support\Models\Scopes\CompanyScope;

class VariantUsage
{
    /**
     * Dropping an attribute invalidates every combination, so all of the product's variants go
     * with it — which is only safe while none of them is referenced elsewhere.
     */
    public static function productHasVariantsInUse(int|string $productId): bool
    {
        return ProductUsageRegistry::isAnyProductInUse(
            Product::query()
                ->withoutGlobalScope(CompanyScope::class)
                ->where('parent_id', $productId)
                ->pluck('id')
        );
    }

    /**
     * Deleting an attribute option cascades its product values away, orphaning the variants built
     * from them — and a database cascade fires no model events, so this has to be checked before
     * the option itself goes.
     *
     * @param  array<int, int|string>|Collection  $attributeOptionIds
     */
    public static function optionsHaveVariantsInUse($attributeOptionIds): bool
    {
        return static::valuesHaveVariantsInUse(
            ProductAttributeValue::query()
                ->whereIn('attribute_option_id', collect($attributeOptionIds)->all())
                ->pluck('id')
                ->all()
        );
    }

    /**
     * Regenerating variants destroys the ones built from the dropped values.
     *
     * @param  array<int, int|string>  $productAttributeValueIds
     */
    public static function valuesHaveVariantsInUse(array $productAttributeValueIds): bool
    {
        if ($productAttributeValueIds === []) {
            return false;
        }

        return ProductUsageRegistry::isAnyProductInUse(
            ProductCombination::query()
                ->whereIn('product_attribute_value_id', $productAttributeValueIds)
                ->distinct()
                ->pluck('product_id')
        );
    }
}
