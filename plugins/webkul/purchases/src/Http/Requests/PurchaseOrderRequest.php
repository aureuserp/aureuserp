<?php

namespace Webkul\Purchase\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Webkul\Product\Models\Product;

class PurchaseOrderRequest extends FormRequest
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
            'partner_id'                   => [($isUpdate ? 'sometimes|required' : 'required'), 'integer', 'exists:partners_partners,id'],
            'currency_id'                  => [($isUpdate ? 'sometimes|required' : 'required'), 'integer', 'exists:currencies,id'],
            'ordered_at'                   => [($isUpdate ? 'sometimes|required' : 'required'), 'date'],
            'company_id'                   => [($isUpdate ? 'sometimes|required' : 'required'), 'integer', 'exists:companies,id'],
            'partner_reference'            => ['nullable', 'string', 'max:255'],
            'requisition_id'               => ['nullable', 'integer', 'exists:purchases_requisitions,id'],
            'planned_at'                   => ['nullable', 'date'],
            'user_id'                      => ['nullable', 'integer', 'exists:users,id'],
            'payment_term_id'              => ['nullable', 'integer', 'exists:accounts_payment_terms,id'],
            'incoterm_id'                  => ['nullable', 'integer', 'exists:accounts_incoterms,id'],
            'description'                  => ['nullable', 'string'],
            'origin'                       => ['nullable', 'string', 'max:255'],
            'lines'                        => [($isUpdate ? 'sometimes|required' : 'required'), 'array', 'min:1'],
            'lines.*.id'                   => ['nullable', 'integer', 'exists:purchases_order_lines,id'],
            'lines.*.product_id'           => ['required', 'integer', 'exists:products_products,id'],
            'lines.*.planned_at'           => ['required', 'date'],
            'lines.*.product_qty'          => ['required', 'numeric', 'min:0', 'max:99999999999'],
            'lines.*.uom_id'               => ['nullable', 'integer', 'exists:unit_of_measures,id'],
            'lines.*.product_packaging_qty'=> ['nullable', 'numeric', 'min:0', 'max:99999999999'],
            'lines.*.product_packaging_id' => ['nullable', 'integer', 'exists:products_packagings,id'],
            'lines.*.price_unit'           => ['required', 'numeric', 'min:0', 'max:99999999999'],
            'lines.*.taxes'                => ['nullable', 'array'],
            'lines.*.taxes.*'              => ['integer', 'exists:accounts_taxes,id'],
            'lines.*.discount'             => ['nullable', 'numeric', 'min:0', 'max:100'],
            'lines.*.qty_received'         => ['nullable', 'numeric', 'min:0', 'max:99999999999'],
            'lines.*.qty_received_manual'  => ['nullable', 'numeric', 'min:0', 'max:99999999999'],
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
                                "The product '{$product->name}' is configurable and cannot be used in purchase orders. Please select a product variant instead."
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
                'description' => 'Vendor partner ID.',
                'example'     => 12,
            ],
            'currency_id' => [
                'description' => 'Currency ID.',
                'example'     => 1,
            ],
            'ordered_at' => [
                'description' => 'Order deadline datetime.',
                'example'     => '2026-02-20 10:00:00',
            ],
            'company_id' => [
                'description' => 'Company ID.',
                'example'     => 1,
            ],
            'partner_reference' => [
                'description' => 'Vendor reference.',
                'example'     => 'VEN-REF-009',
            ],
            'requisition_id' => [
                'description' => 'Purchase agreement ID.',
                'example'     => 5,
            ],
            'user_id' => [
                'description' => 'Buyer user ID.',
                'example'     => 2,
            ],
            'incoterm_id' => [
                'description' => 'Incoterm ID.',
                'example'     => 3,
            ],
            'payment_term_id' => [
                'description' => 'Payment term ID.',
                'example'     => 4,
            ],
            'description' => [
                'description' => 'Terms and conditions / internal description.',
                'example'     => 'Please ship in one lot.',
            ],
            'lines' => [
                'description' => 'Array of purchase order lines with writable fields from the order form.',
                'example'     => [
                    [
                        'product_id'            => 101,
                        'planned_at'            => '2026-02-28 12:00:00',
                        'product_qty'           => 20,
                        'uom_id'                => 1,
                        'product_packaging_id'  => null,
                        'product_packaging_qty' => 0,
                        'price_unit'            => 80,
                        'discount'              => 5,
                        'qty_received'          => 0,
                        'qty_received_manual'   => 0,
                        'taxes'                 => [1, 2],
                    ],
                ],
            ],
        ];
    }
}
