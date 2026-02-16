<?php

namespace Webkul\Account\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Webkul\Account\Models\Product;

class InvoiceRequest extends FormRequest
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
        $rules = [
            'partner_id'                       => 'required|integer|exists:partners_partners,id',
            'currency_id'                      => 'required|integer|exists:currencies,id',
            'journal_id'                       => 'required|integer|exists:accounts_journals,id',
            'invoice_date'                     => 'required|date',
            'invoice_date_due'                 => 'nullable|date|prohibited_if:invoice_payment_term_id,*',
            'invoice_payment_term_id'          => 'nullable|integer|exists:accounts_payment_terms,id|prohibited_if:invoice_date_due,*',
            'fiscal_position_id'               => 'nullable|integer|exists:accounts_fiscal_positions,id',
            'invoice_user_id'                  => 'nullable|integer|exists:users,id',
            'partner_bank_id'                  => 'nullable|integer|exists:partners_bank_accounts,id',
            'invoice_incoterm_id'              => 'nullable|integer|exists:accounts_incoterms,id',
            'invoice_cash_rounding_id'         => 'nullable|integer|exists:accounts_cash_roundings,id',
            'preferred_payment_method_line_id' => 'nullable|integer|exists:accounts_payment_method_lines,id',
            'reference'                        => 'nullable|string|max:255',
            'payment_reference'                => 'nullable|string|max:255',
            'narration'                        => 'nullable|string',
            'incoterm_location'                => 'nullable|string|max:255',
            'delivery_date'                    => 'nullable|date',
            'invoice_lines'                    => 'required|array|min:1',
            'invoice_lines.*.product_id'       => 'required|integer|exists:products_products,id',
            'invoice_lines.*.quantity'         => 'required|numeric|min:0.0001',
            'invoice_lines.*.uom_id'           => 'required|integer|exists:unit_of_measures,id',
            'invoice_lines.*.price_unit'       => 'required|numeric',
            'invoice_lines.*.discount'         => 'nullable|numeric|min:0|max:100',
            'invoice_lines.*.taxes'            => 'nullable|array',
            'invoice_lines.*.taxes.*'          => 'integer|exists:accounts_taxes,id',
            'invoice_lines.*.id'               => 'nullable|integer|exists:accounts_account_move_lines,id',
        ];

        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            foreach ($rules as $key => $rule) {
                if (! str_starts_with($key, 'invoice_lines')) {
                    if (is_string($rule) && str_starts_with($rule, 'required')) {
                        $rules[$key] = str_replace('required', 'sometimes|required', $rule);
                    }
                }
            }
        }

        return $rules;
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if (! $this->input('invoice_date_due') && ! $this->input('invoice_payment_term_id')) {
                $validator->errors()->add(
                    'invoice_date_due',
                    'Either invoice due date or payment term must be provided.'
                );
                $validator->errors()->add(
                    'invoice_payment_term_id',
                    'Either invoice due date or payment term must be provided.'
                );
            }

            $invoiceLines = $this->input('invoice_lines', []);
            $productIds = collect($invoiceLines)->pluck('product_id')->filter()->unique();

            if ($productIds->isNotEmpty()) {
                $configurableProducts = Product::whereIn('id', $productIds)
                    ->where('is_configurable', true)
                    ->get(['id', 'name'])
                    ->keyBy('id');

                if ($configurableProducts->isNotEmpty()) {
                    foreach ($invoiceLines as $index => $line) {
                        if (isset($line['product_id']) && isset($configurableProducts[$line['product_id']])) {
                            $product = $configurableProducts[$line['product_id']];

                            $validator->errors()->add(
                                "invoice_lines.{$index}.product_id",
                                "The product '{$product->name}' is configurable and cannot be used in invoices. Please select a product variant instead."
                            );
                        }
                    }
                }
            }
        });
    }

    /**
     * Get custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'invoice_date_due.prohibited_if'        => 'The invoice due date cannot be provided when payment term is specified.',
            'invoice_payment_term_id.prohibited_if' => 'The payment term cannot be provided when invoice due date is specified.',
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
            'partner_id' => [
                'description' => 'The customer/partner ID.',
                'example'     => 1,
            ],
            'currency_id' => [
                'description' => 'The currency ID.',
                'example'     => 1,
            ],
            'journal_id' => [
                'description' => 'The journal ID for invoices.',
                'example'     => 1,
            ],
            'invoice_date' => [
                'description' => 'The invoice date.',
                'example'     => '2026-02-16',
            ],
            'invoice_date_due' => [
                'description' => 'The invoice due date. Cannot be provided if invoice_payment_term_id is specified.',
                'example'     => '2026-03-16',
            ],
            'invoice_payment_term_id' => [
                'description' => 'The payment term ID. Cannot be provided if invoice_date_due is specified.',
                'example'     => 1,
            ],
            'fiscal_position_id' => [
                'description' => 'The fiscal position ID.',
                'example'     => 1,
            ],
            'invoice_user_id' => [
                'description' => 'The salesperson/user ID.',
                'example'     => 1,
            ],
            'partner_bank_id' => [
                'description' => 'The recipient bank account ID.',
                'example'     => 1,
            ],
            'invoice_incoterm_id' => [
                'description' => 'The incoterm ID.',
                'example'     => 1,
            ],
            'invoice_cash_rounding_id' => [
                'description' => 'The cash rounding ID.',
                'example'     => 1,
            ],
            'preferred_payment_method_line_id' => [
                'description' => 'The preferred payment method line ID.',
                'example'     => 1,
            ],
            'reference' => [
                'description' => 'The customer reference.',
                'example'     => 'INV-2026-001',
            ],
            'payment_reference' => [
                'description' => 'The payment reference.',
                'example'     => 'PAY-001',
            ],
            'narration' => [
                'description' => 'Terms and conditions / Internal notes.',
                'example'     => 'Additional information',
            ],
            'incoterm_location' => [
                'description' => 'The incoterm location.',
                'example'     => 'New York',
            ],
            'delivery_date' => [
                'description' => 'The delivery date.',
                'example'     => '2026-02-20',
            ],
            'invoice_lines' => [
                'description' => 'Array of invoice line items with product details, quantities, prices, and taxes.',
                'example'     => [
                    [
                        'product_id'  => 1,
                        'quantity'    => 2,
                        'uom_id'      => 1,
                        'price_unit'  => 100.00,
                        'discount'    => 10,
                        'taxes'       => [1, 2],
                    ],
                ],
            ],
        ];
    }
}
