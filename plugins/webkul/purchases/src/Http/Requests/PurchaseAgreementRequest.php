<?php

namespace Webkul\Purchase\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Webkul\Product\Models\Product;
use Webkul\Purchase\Enums\RequisitionType;

class PurchaseAgreementRequest extends FormRequest
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
            'partner_id'        => ($isUpdate ? 'sometimes|' : '').'required|integer|exists:partners_partners,id',
            'type'              => ($isUpdate ? 'sometimes|' : '').'required|string|in:'.implode(',', array_column(RequisitionType::cases(), 'value')),
            'currency_id'       => ($isUpdate ? 'sometimes|' : '').'required|integer|exists:currencies,id',
            'company_id'        => ($isUpdate ? 'sometimes|' : '').'required|integer|exists:companies,id',
            'user_id'           => 'nullable|integer|exists:users,id',
            'starts_at'         => 'nullable|date|after_or_equal:today',
            'ends_at'           => 'nullable|date|after_or_equal:starts_at',
            'reference'         => 'nullable|string|max:255',
            'description'       => 'nullable|string',
            'lines'             => ($isUpdate ? 'sometimes|' : '').'required|array|min:1',
            'lines.*.id'        => 'nullable|integer|exists:purchases_requisition_lines,id',
            'lines.*.product_id'=> 'required|integer|exists:products_products,id',
            'lines.*.qty'       => 'required|numeric|min:0|max:99999999999',
            'lines.*.uom_id'    => 'nullable|integer|exists:unit_of_measures,id',
            'lines.*.price_unit'=> 'required|numeric|min:0|max:99999999999',
        ];

        return $rules;
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $type = $this->input('type');

            if ($type === RequisitionType::BLANKET_ORDER->value && ! $this->input('starts_at')) {
                $validator->errors()->add('starts_at', 'The starts at field is required when agreement type is blanket order.');
            }

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
                                "The product '{$product->name}' is configurable and cannot be used in purchase agreements. Please select a product variant instead."
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
            'type' => [
                'description' => 'Agreement type: blanket_order or purchase_template.',
                'example'     => 'blanket_order',
            ],
            'currency_id' => [
                'description' => 'Currency ID.',
                'example'     => 1,
            ],
            'company_id' => [
                'description' => 'Company ID.',
                'example'     => 1,
            ],
            'user_id' => [
                'description' => 'Buyer user ID.',
                'example'     => 2,
            ],
            'starts_at' => [
                'description' => 'Agreement validity start date (YYYY-MM-DD).',
                'example'     => '2026-02-20',
            ],
            'ends_at' => [
                'description' => 'Agreement validity end date (YYYY-MM-DD).',
                'example'     => '2026-08-20',
            ],
            'reference' => [
                'description' => 'Reference text.',
                'example'     => 'AG-2026-Q1',
            ],
            'description' => [
                'description' => 'Terms and conditions / notes.',
                'example'     => 'Blanket agreement for monthly supply.',
            ],
            'lines' => [
                'description' => 'Array of agreement lines with writable fields from the purchase agreement form.',
                'example'     => [
                    [
                        'product_id' => 101,
                        'qty'        => 100,
                        'uom_id'     => 1,
                        'price_unit' => 70,
                    ],
                ],
            ],
        ];
    }
}
