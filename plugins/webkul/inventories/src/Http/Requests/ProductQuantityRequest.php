<?php

namespace Webkul\Inventory\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Webkul\Inventory\Enums\ProductTracking;
use Webkul\Inventory\Models\Lot;
use Webkul\Inventory\Models\Package;
use Webkul\Inventory\Models\Product;

class ProductQuantityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH');
        $requiredRule = $isUpdate ? ['sometimes', 'required'] : ['required'];

        return [
            'location_id'        => [...$requiredRule, 'integer', 'exists:inventories_locations,id'],
            'product_id'         => [...$requiredRule, 'integer', 'exists:products_products,id'],
            'storage_category_id'=> ['nullable', 'integer', 'exists:inventories_storage_categories,id'],
            'lot_id'             => ['nullable', 'integer', 'exists:inventories_lots,id'],
            'package_id'         => ['nullable', 'integer', 'exists:inventories_packages,id'],
            'partner_id'         => ['nullable', 'integer', 'exists:partners_partners,id'],
            'user_id'            => ['nullable', 'integer', 'exists:users,id'],
            'company_id'         => ['nullable', 'integer', 'exists:companies,id'],
            'counted_quantity'   => [...$requiredRule, 'numeric', 'min:0', 'max:99999999999'],
            'scheduled_at'       => ['nullable', 'date'],
        ];
    }

    public function bodyParameters(): array
    {
        return [
            'location_id' => [
                'description' => 'Inventory location ID.',
                'example'     => 1,
            ],
            'product_id' => [
                'description' => 'Product ID. Configurable products are not allowed; pass a variant product ID instead.',
                'example'     => 1,
            ],
            'storage_category_id' => [
                'description' => 'Storage category ID.',
                'example'     => 1,
            ],
            'lot_id' => [
                'description' => 'Lot ID. Required for lot/serial tracked products and must belong to the selected product.',
                'example'     => 1,
            ],
            'package_id' => [
                'description' => 'Package ID. Must belong to the selected location.',
                'example'     => 1,
            ],
            'partner_id' => [
                'description' => 'Owner partner ID.',
                'example'     => 1,
            ],
            'user_id' => [
                'description' => 'Responsible user ID.',
                'example'     => 1,
            ],
            'company_id' => [
                'description' => 'Company ID.',
                'example'     => 1,
            ],
            'counted_quantity' => [
                'description' => 'Counted quantity entered during inventory adjustment. For serial-tracked products, this must be 0 or 1.',
                'example'     => 25,
            ],
            'scheduled_at' => [
                'description' => 'Scheduled inventory date.',
                'example'     => '2026-02-25',
            ],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            $productId = $this->input('product_id');

            if (! $productId) {
                return;
            }

            $product = Product::query()->find($productId);

            if (! $product) {
                return;
            }

            if ($product->is_configurable) {
                $validator->errors()->add(
                    'product_id',
                    "The product '{$product->name}' is configurable and cannot be used for quantity adjustment. Please select a product variant instead."
                );
            }

            $lotId = $this->input('lot_id');
            $packageId = $this->input('package_id');
            $locationId = $this->input('location_id');
            $countedQuantity = $this->input('counted_quantity');

            if (in_array($product->tracking, [ProductTracking::LOT, ProductTracking::SERIAL], true) && empty($lotId)) {
                $validator->errors()->add(
                    'lot_id',
                    'The lot id field is required for tracked products.'
                );
            }

            if ($lotId) {
                $lotBelongsToProduct = Lot::query()
                    ->whereKey($lotId)
                    ->where('product_id', $product->id)
                    ->exists();

                if (! $lotBelongsToProduct) {
                    $validator->errors()->add(
                        'lot_id',
                        'The selected lot does not belong to the selected product.'
                    );
                }
            }

            if ($packageId && $locationId) {
                $packageBelongsToLocation = Package::query()
                    ->whereKey($packageId)
                    ->where('location_id', $locationId)
                    ->exists();

                if (! $packageBelongsToLocation) {
                    $validator->errors()->add(
                        'package_id',
                        'The selected package does not belong to the selected location.'
                    );
                }
            }

            if ($product->tracking === ProductTracking::SERIAL && is_numeric($countedQuantity) && (float) $countedQuantity > 1) {
                $validator->errors()->add(
                    'counted_quantity',
                    'The counted quantity field must be less than or equal to 1 for serial-tracked products.'
                );
            }
        });
    }
}
