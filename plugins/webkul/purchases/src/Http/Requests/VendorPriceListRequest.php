<?php

namespace Webkul\Purchase\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Webkul\Product\Models\Product;

class VendorPriceListRequest extends FormRequest
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

        $rules = [
            'partner_id'    => ($isUpdate ? 'sometimes|' : '').'required|integer|exists:partners_partners,id',
            'product_id'    => ($isUpdate ? 'sometimes|' : '').'required|integer|exists:products_products,id',
            'currency_id'   => ($isUpdate ? 'sometimes|' : '').'required|integer|exists:currencies,id',
            'company_id'    => 'nullable|integer|exists:companies,id',
            'product_name'  => 'nullable|string|max:255',
            'product_code'  => 'nullable|string|max:255',
            'delay'         => 'nullable|integer|min:0|max:99999999',
            'min_qty'       => 'nullable|numeric|min:0|max:99999999999',
            'price'         => 'nullable|numeric|min:0|max:99999999999',
            'discount'      => 'nullable|numeric|min:0|max:99999999999',
            'starts_at'     => 'nullable|date',
            'ends_at'       => 'nullable|date|after_or_equal:starts_at',
        ];

        return $rules;
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $productId = $this->input('product_id');

            if (! $productId) {
                return;
            }

            $product = Product::query()
                ->select(['id', 'name', 'is_configurable'])
                ->find($productId);

            if ($product?->is_configurable) {
                $validator->errors()->add(
                    'product_id',
                    "The product '{$product->name}' is configurable and cannot be used as a vendor price line. Please select a product variant instead."
                );
            }
        });
    }

    /**
     * Get body parameters for API documentation.
     *
     * @return array<string, array<string, mixed>>
     */
    public function bodyParameters(): array
    {
        return [
            'partner_id' => [
                'description' => 'Vendor partner ID.',
                'example'     => 12,
            ],
            'product_id' => [
                'description' => 'Product ID (non-configurable product/variant).',
                'example'     => 101,
            ],
            'currency_id' => [
                'description' => 'Currency ID.',
                'example'     => 1,
            ],
            'company_id' => [
                'description' => 'Company ID.',
                'example'     => 1,
            ],
            'delay' => [
                'description' => 'Delivery lead time in days.',
                'example'     => 5,
            ],
            'min_qty' => [
                'description' => 'Minimum quantity for this vendor price.',
                'example'     => 10,
            ],
            'price' => [
                'description' => 'Unit price for the vendor.',
                'example'     => 125.50,
            ],
            'discount' => [
                'description' => 'Discount percentage/value as configured by module usage.',
                'example'     => 5,
            ],
            'starts_at' => [
                'description' => 'Validity start date (YYYY-MM-DD).',
                'example'     => '2026-02-20',
            ],
            'ends_at' => [
                'description' => 'Validity end date (YYYY-MM-DD).',
                'example'     => '2026-12-31',
            ],
            'product_name' => [
                'description' => 'Vendor-side product name.',
                'example'     => 'ACME Bolt M8',
            ],
            'product_code' => [
                'description' => 'Vendor-side product code.',
                'example'     => 'ACM-M8-001',
            ],
        ];
    }
}
