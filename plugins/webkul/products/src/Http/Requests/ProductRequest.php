<?php

namespace Webkul\Product\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Webkul\Product\Enums\ProductType;

class ProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH');

        return [
            'type'                 => ($isUpdate ? 'sometimes|' : '').'required|string|in:'.implode(',', array_column(ProductType::cases(), 'value')),
            'name'                 => ($isUpdate ? 'sometimes|' : '').'required|string|max:255',
            'service_tracking'     => 'nullable|string|max:255',
            'reference'            => 'nullable|string|max:255',
            'barcode'              => 'nullable|string|max:255',
            'price'                => ($isUpdate ? 'sometimes|' : '').'required|numeric|min:0',
            'cost'                 => 'nullable|numeric|min:0',
            'volume'               => 'nullable|numeric|min:0|max:99999999999',
            'weight'               => 'nullable|numeric|min:0|max:99999999999',
            'description'          => 'nullable|string',
            'description_purchase' => 'nullable|string',
            'description_sale'     => 'nullable|string',
            'enable_sales'         => 'nullable|boolean',
            'enable_purchase'      => 'nullable|boolean',
            'is_favorite'          => 'nullable|boolean',
            'images'               => 'nullable|array',
            'images.*'             => 'nullable|string',
            'uom_id'               => 'nullable|integer',
            'uom_po_id'            => 'nullable|integer',
            'category_id'          => ($isUpdate ? 'sometimes|' : '').'required|integer|exists:products_categories,id',
            'company_id'           => 'nullable|integer',
            'tags'                 => 'nullable|array',
            'tags.*'               => 'integer|exists:products_tags,id',
        ];
    }

    /**
     * Get body parameters for API documentation.
     *
     * @return array<string, array<string, mixed>>
     */
    public function bodyParameters(): array
    {
        return [
            'type' => [
                'description' => 'Product type: goods, service, consu (consumable).',
                'example'     => 'goods',
            ],
            'name' => [
                'description' => 'Product name (max 255 characters).',
                'example'     => 'Laptop Computer',
            ],
            'reference' => [
                'description' => 'Internal reference code (max 255 characters).',
                'example'     => 'PROD-001',
            ],
            'barcode' => [
                'description' => 'Product barcode (max 255 characters).',
                'example'     => '1234567890',
            ],
            'price' => [
                'description' => 'Sales price (minimum 0).',
                'example'     => 999.99,
            ],
            'cost' => [
                'description' => 'Product cost (minimum 0).',
                'example'     => 750.00,
            ],
            'weight' => [
                'description' => 'Product weight (min 0, max 99999999999).',
                'example'     => 2.5,
            ],
            'volume' => [
                'description' => 'Product volume (min 0, max 99999999999).',
                'example'     => 0.5,
            ],
            'description' => [
                'description' => 'Product description.',
                'example'     => 'High-performance laptop with latest specifications.',
            ],
            'description_purchase' => [
                'description' => 'Purchase description.',
                'example'     => 'Enterprise laptop for business use.',
            ],
            'description_sale' => [
                'description' => 'Sales description.',
                'example'     => 'Perfect for professionals and power users.',
            ],
            'enable_sales' => [
                'description' => 'Whether this product can be sold.',
                'example'     => true,
            ],
            'enable_purchase' => [
                'description' => 'Whether this product can be purchased.',
                'example'     => true,
            ],
            'is_favorite' => [
                'description' => 'Mark this product as favorite.',
                'example'     => false,
            ],
            'is_configurable' => [
                'description' => 'Whether this product has variants/configurations.',
                'example'     => false,
            ],
            'images' => [
                'description' => 'Array of product image URLs or paths.',
                'example'     => ['image1.jpg', 'image2.jpg'],
            ],
            'category_id' => [
                'description' => 'Product category ID.',
                'example'     => 1,
            ],
            'company_id' => [
                'description' => 'Company ID this product belongs to.',
                'example'     => 1,
            ],
            'uom_id' => [
                'description' => 'Unit of measure ID.',
                'example'     => 1,
            ],
            'uom_po_id' => [
                'description' => 'Purchase unit of measure ID.',
                'example'     => 1,
            ],
            'tags' => [
                'description' => 'Array of tag IDs to associate with this product.',
                'example'     => [1, 2, 3],
            ],
        ];
    }
}
