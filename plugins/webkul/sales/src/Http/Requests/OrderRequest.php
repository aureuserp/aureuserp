<?php

namespace Webkul\Sale\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Webkul\Product\Models\Product;

class OrderRequest extends FormRequest
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
            'partner_id'                    => ($isUpdate ? 'sometimes|' : '').'required|integer|exists:partners_partners,id',
            'payment_term_id'               => ($isUpdate ? 'sometimes|' : '').'required|integer|exists:accounts_payment_terms,id',
            'date_order'                    => ($isUpdate ? 'sometimes|' : '').'required|date',
            'validity_date'                 => 'nullable|date',
            'commitment_date'               => 'nullable|date',
            'client_order_ref'              => 'nullable|string|max:255',
            'origin'                        => 'nullable|string|max:255',
            'note'                          => 'nullable|string',
            'user_id'                       => 'nullable|integer|exists:users,id',
            'company_id'                    => 'nullable|integer|exists:companies,id',
            'currency_id'                   => 'nullable|integer|exists:currencies,id',
            'campaign_id'                   => 'nullable|integer|exists:utm_campaigns,id',
            'utm_source_id'                 => 'nullable|integer|exists:utm_sources,id',
            'medium_id'                     => 'nullable|integer|exists:utm_mediums,id',
            'sales_order_tags'              => 'nullable|array',
            'sales_order_tags.*'            => 'integer|exists:sales_tags,id',
            'lines'                         => ($isUpdate ? 'sometimes|' : '').'required|array|min:1',
            'lines.*.id'                    => 'nullable|integer|exists:sales_order_lines,id',
            'lines.*.product_id'            => 'required|integer|exists:products_products,id',
            'lines.*.product_qty'           => 'required|numeric|min:0|max:99999999999',
            'lines.*.qty_delivered'         => 'nullable|numeric|min:0|max:99999999999',
            'lines.*.product_packaging_qty' => 'nullable|numeric|min:0|max:99999999999',
            'lines.*.price_unit'            => 'required|numeric|min:0|max:99999999999',
            'lines.*.discount'              => 'nullable|numeric|min:0|max:100',
            'lines.*.customer_lead'         => 'nullable|numeric|min:0|max:99999999999',
            'lines.*.product_uom_id'        => 'nullable|integer|exists:unit_of_measures,id',
            'lines.*.product_packaging_id'  => 'nullable|integer|exists:products_packagings,id',
            'lines.*.taxes'                 => 'nullable|array',
            'lines.*.taxes.*'               => 'integer|exists:accounts_taxes,id',
        ];

        return $rules;
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $lines = $this->input('lines', []);
            $productIds = collect($lines)->pluck('product_id')->filter()->unique();

            if ($productIds->isNotEmpty()) {
                $configurableProducts = Product::whereIn('id', $productIds)
                    ->where('is_configurable', true)
                    ->get(['id', 'name'])
                    ->keyBy('id');

                if ($configurableProducts->isNotEmpty()) {
                    foreach ($lines as $index => $line) {
                        if (isset($line['product_id']) && isset($configurableProducts[$line['product_id']])) {
                            $product = $configurableProducts[$line['product_id']];

                            $validator->errors()->add(
                                "lines.{$index}.product_id",
                                "The product '{$product->name}' is configurable and cannot be used in orders. Please select a product variant instead."
                            );
                        }
                    }
                }
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
                'description' => 'Customer (partner) ID.',
                'example'     => 1,
            ],
            'payment_term_id' => [
                'description' => 'Payment term ID.',
                'example'     => 1,
            ],
            'date_order' => [
                'description' => 'Order date in YYYY-MM-DD format.',
                'example'     => '2026-02-18',
            ],
            'validity_date' => [
                'description' => 'Quotation validity date in YYYY-MM-DD format.',
                'example'     => '2026-02-28',
            ],
            'company_id' => [
                'description' => 'Company ID.',
                'example'     => 1,
            ],
            'currency_id' => [
                'description' => 'Currency ID.',
                'example'     => 1,
            ],
            'sales_order_tags' => [
                'description' => 'Order tag IDs.',
                'example'     => [1, 2],
            ],
            'note' => [
                'description' => 'Terms and conditions or internal note.',
                'example'     => 'Delivery within 7 business days.',
            ],
            'lines' => [
                'description' => 'Array of order line items with writable fields from the quotation form.',
                'example'     => [
                    [
                        'product_id'            => 10,
                        'product_qty'           => 2,
                        'product_uom_id'        => 1,
                        'price_unit'            => 120.5,
                        'discount'              => 5,
                        'customer_lead'         => 0,
                        'product_packaging_id'  => null,
                        'product_packaging_qty' => 0,
                        'taxes'                 => [1, 2],
                    ],
                ],
            ],
        ];
    }
}
