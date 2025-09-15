<?php

namespace Webkul\Product\Filament\Resources\ProductResource\Actions;

use Closure;
use Exception;
use Filament\Actions\Action;
use Filament\Actions\Concerns\CanCustomizeProcess;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Webkul\Product\Filament\Resources\ProductResource\Pages\ManageAttributes;
use Webkul\Product\Models\Product;
use Webkul\Product\Models\ProductAttribute;
use Webkul\Product\Models\ProductCombination;

class GenerateVariantsAction extends Action
{
    use CanCustomizeProcess;

    protected Model|string|array|Closure|null $record = null;

    public static function getDefaultName(): ?string
    {
        return 'products.generate.variants';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->icon('heroicon-o-cube')
            ->label(__('products::filament/resources/product/actions/generate-variants.label'))
            ->color('primary')
            ->action(function (ManageAttributes $livewire) {
                $this->record = $livewire->getRecord();
                $this->generateVariants();

                $this->record->is_configurable = true;
                $this->record->save();
            })
            ->hidden(fn (ManageAttributes $livewire) => $livewire->getRecord()->attributes->isEmpty());
    }

    protected function generateVariants(): void
    {
        try {
            $attributes = $this->record->attributes()
                ->with(['values', 'attribute', 'options'])
                ->get();

            if ($attributes->count() === 1) {
                $this->handleSingleAttributeVariants($attributes->first());
            } else {
                $this->handleMultipleAttributeVariants($attributes);
            }

            Notification::make()
                ->success()
                ->title(__('products::filament/resources/product/actions/generate-variants.notification.success.title'))
                ->body(__('products::filament/resources/product/actions/generate-variants.notification.success.body'))
                ->send();
        } catch (Exception $e) {
            Notification::make()
                ->danger()
                ->title(__('products::filament/resources/product/actions/generate-variants.notification.error.title'))
                ->body(__('products::filament/resources/product/actions/generate-variants.notification.error.body'))
                ->send();
        }
    }

    protected function handleSingleAttributeVariants(ProductAttribute $attribute): void
    {
        $attributeValues = $attribute->values;

        $existingVariants = Product::where('parent_id', $this->record->id)->get();
        $processedVariantIds = [];

        foreach ($attributeValues as $value) {
            $existingVariant = null;
            foreach ($existingVariants as $variant) {
                $combination = ProductCombination::where('product_id', $variant->id)
                    ->where('product_attribute_value_id', $value->id)
                    ->first();

                if ($combination) {
                    $existingVariant = $variant;
                    break;
                }
            }

            if ($existingVariant) {
                $this->updateVariant($existingVariant, [$value]);
                $processedVariantIds[] = $existingVariant->id;
            } else {
                $variant = $this->createVariant($this->record, [$value]);

                ProductCombination::create([
                    'product_id'                 => $variant->id,
                    'product_attribute_value_id' => $value->id,
                ]);

                $processedVariantIds[] = $variant->id;
            }
        }

        $variantsToDelete = $existingVariants->whereNotIn('id', $processedVariantIds);
        foreach ($variantsToDelete as $variant) {
            ProductCombination::where('product_id', $variant->id)->delete();

            $variant->forceDelete();
        }
    }

    protected function handleMultipleAttributeVariants(Collection $attributes): void
    {
        $existingVariants = Product::where('parent_id', $this->record->id)->get();
        $processedVariantIds = [];

        $combinations = $this->generateAttributeCombinations($attributes);

        foreach ($combinations as $combination) {
            $existingVariant = null;

            foreach ($existingVariants as $variant) {
                $variantCombinations = ProductCombination::where('product_id', $variant->id)
                    ->pluck('product_attribute_value_id')
                    ->toArray();

                $combinationIds = collect($combination)
                    ->pluck('id')
                    ->toArray();

                if (empty(array_diff($variantCombinations, $combinationIds)) && empty(array_diff($combinationIds, $variantCombinations))) {
                    $existingVariant = $variant;

                    break;
                }
            }

            if ($existingVariant) {
                $this->updateVariant($existingVariant, $combination);

                $processedVariantIds[] = $existingVariant->id;
            } else {
                $variant = $this->createVariant($this->record, $combination);

                foreach ($combination as $attributeValue) {
                    ProductCombination::create([
                        'product_id'                 => $variant->id,
                        'product_attribute_value_id' => $attributeValue->id,
                    ]);
                }

                $processedVariantIds[] = $variant->id;
            }
        }

        $variantsToDelete = $existingVariants->whereNotIn('id', $processedVariantIds);

        foreach ($variantsToDelete as $variant) {
            ProductCombination::where('product_id', $variant->id)->delete();

            $variant->forceDelete();
        }
    }

    protected function generateAttributeCombinations(Collection $attributes, $currentCombination = [], $index = 0): array
    {
        $combinations = [];

        if ($index >= $attributes->count()) {
            return [$currentCombination];
        }

        $currentAttribute = $attributes[$index];
        $attributeValues = $currentAttribute->values;

        foreach ($attributeValues as $value) {
            $newCombination = array_merge($currentCombination, [$value]);
            $combinations = array_merge(
                $combinations,
                $this->generateAttributeCombinations($attributes, $newCombination, $index + 1)
            );
        }

        return $combinations;
    }

    protected function createVariant(Product $parent, array $attributeValues): Product
    {
        $variantName = $parent->name.' - '.collect($attributeValues)
            ->map(fn ($value) => $value->attributeOption->name)
            ->implode(' / ');

        $extraPrice = collect($attributeValues)->sum('extra_price');

        $variant = new Product;

        $variant->fill([
            'type'                 => $parent->type,
            'name'                 => $variantName,
            'price'                => $parent->price + $extraPrice,
            'cost'                 => $parent->cost,
            'enable_sales'         => $parent->enable_sales,
            'enable_purchase'      => $parent->enable_purchase,
            'parent_id'            => $parent->id,
            'company_id'           => $parent->company_id,
            'creator_id'           => Auth::id(),
            'uom_id'               => $parent->uom_id,
            'uom_po_id'            => $parent->uom_po_id,
            'category_id'          => $parent->category_id,
            'volume'               => $parent->volume,
            'weight'               => $parent->weight,
            'description'          => $parent->description,
            'description_purchase' => $parent->description_purchase,
            'description_sale'     => $parent->description_sale,
            'barcode'              => null,
            'reference'            => $parent->reference.'-'.strtolower(str_replace(' ', '-', $variantName)),
            'images'               => $parent->images,
        ]);

        $variant->save();

        return $variant;
    }

    protected function updateVariant(Product $variant, array $attributeValues): void
    {
        $variantName = $this->record->name.' - '.collect($attributeValues)
            ->map(fn ($value) => $value->attributeOption->name)
            ->implode(' / ');

        $extraPrice = collect($attributeValues)->sum('extra_price');

        $variant->fill([
            'name'  => $variantName,
            'price' => $this->record->price + $extraPrice,
        ]);

        $variant->save();
    }
}
