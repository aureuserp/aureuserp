<?php

namespace Webkul\Account\Http\Requests;

use Webkul\Product\Http\Requests\CategoryRequest as BaseCategoryRequest;

class CategoryRequest extends BaseCategoryRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'property_account_income_id'       => 'nullable|integer|exists:accounts_accounts,id',
            'property_account_expense_id'      => 'nullable|integer|exists:accounts_accounts,id',
            'property_account_down_payment_id' => 'nullable|integer|exists:accounts_accounts,id',
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
                'description' => 'Income account ID for this category.',
                'example'     => 1,
            ],
            'property_account_expense_id' => [
                'description' => 'Expense account ID for this category.',
                'example'     => 2,
            ],
            'property_account_down_payment_id' => [
                'description' => 'Down payment account ID for this category.',
                'example'     => 3,
            ],
        ]);
    }
}
