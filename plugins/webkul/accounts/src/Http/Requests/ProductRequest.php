<?php

namespace Webkul\Account\Http\Requests;

use Webkul\Product\Http\Requests\ProductRequest as BaseProductRequest;

class ProductRequest extends BaseProductRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'property_account_income_id'  => 'nullable|integer|exists:accounts_accounts,id',
            'property_account_expense_id' => 'nullable|integer|exists:accounts_accounts,id',
            'image'                       => 'nullable|string',
            'service_type'                => 'nullable|string',
            'sale_line_warn'              => 'nullable|string',
            'expense_policy'              => 'nullable|string',
            'invoice_policy'              => 'nullable|string|in:order,delivery',
            'sale_line_warn_msg'          => 'nullable|string',
            'sales_ok'                    => 'nullable|boolean',
            'purchase_ok'                 => 'nullable|boolean',
        ]);
    }

    /**
     * Get body parameters for API documentation.
     *
     * @return array<string, array<string, mixed>>
     */
    public function bodyParameters(): array
    {
        return array_merge(parent::bodyParameters(), [
            'property_account_income_id' => [
                'description' => 'Income account ID for this product.',
                'example'     => 1,
            ],
            'property_account_expense_id' => [
                'description' => 'Expense account ID for this product.',
                'example'     => 2,
            ],
            'invoice_policy' => [
                'description' => 'Invoice policy: "order" (invoice on order) or "delivery" (invoice on delivery).',
                'example'     => 'order',
            ],
            'sales_ok' => [
                'description' => 'Whether this product can be sold.',
                'example'     => true,
            ],
            'purchase_ok' => [
                'description' => 'Whether this product can be purchased.',
                'example'     => true,
            ],
        ]);
    }
}
