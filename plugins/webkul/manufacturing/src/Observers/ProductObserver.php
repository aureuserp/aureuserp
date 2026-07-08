<?php

namespace Webkul\Manufacturing\Observers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\ValidationException;
use Webkul\Manufacturing\Models\BillOfMaterialLine;
use Webkul\PluginManager\Package;
use Webkul\Product\Models\Product;
use Webkul\Support\Models\UOM;

class ProductObserver
{
    public function updating(Product $product): void
    {
        if (! Package::isPluginInstalled('manufacturing')
            || ! $product->isDirty(['uom_id', 'uom_po_id'])) {
            return;
        }

        $productUom = UOM::query()->withTrashed()->find($product->uom_id);
        $costUom = UOM::query()->withTrashed()->find($product->uom_po_id) ?? $productUom;

        if (! $costUom) {
            return;
        }

        if ($productUom && $productUom->category_id !== $costUom->category_id) {
            throw ValidationException::withMessages([
                'uom_po_id' => __('The unit of measure and purchase unit of measure must belong to the same category.'),
            ]);
        }

        $hasIncompatibleBillOfMaterialLine = BillOfMaterialLine::query()
            ->where('product_id', $product->getKey())
            ->whereHas('uom', fn (Builder $query): Builder => $query
                ->where('category_id', '!=', $costUom->category_id))
            ->exists();

        if ($hasIncompatibleBillOfMaterialLine) {
            throw ValidationException::withMessages([
                'uom_id' => __('The unit of measure category cannot be changed because this product is used by a bill of materials component with an incompatible unit of measure. Update the bill of materials component first.'),
            ]);
        }
    }
}
