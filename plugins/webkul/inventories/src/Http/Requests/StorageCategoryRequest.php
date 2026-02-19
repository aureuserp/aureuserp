<?php

namespace Webkul\Inventory\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Webkul\Inventory\Enums\AllowNewProduct;

class StorageCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'name'               => ['required', 'string', 'max:255'],
            'max_weight'         => ['nullable', 'numeric', 'min:0', 'max:99999999'],
            'allow_new_products' => ['required', Rule::enum(AllowNewProduct::class)],
            'company_id'         => ['nullable', 'integer', 'exists:companies,id'],
        ];

        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            foreach ($rules as $key => $rule) {
                if (str_contains($key, '.')) {
                    continue;
                }

                if (is_array($rule) && in_array('required', $rule, true)) {
                    $index = array_search('required', $rule, true);

                    if ($index !== false) {
                        unset($rule[$index]);
                    }

                    array_unshift($rule, 'sometimes', 'required');
                    $rules[$key] = array_values($rule);
                }
            }
        }

        return $rules;
    }

    public function bodyParameters(): array
    {
        return [
            'name' => [
                'description' => 'Storage category name.',
                'example'     => 'Heavy Goods',
            ],
            'max_weight' => [
                'description' => 'Maximum allowed weight.',
                'example'     => 500.0,
            ],
            'allow_new_products' => [
                'description' => 'Policy for new products.',
                'example'     => 'mixed',
            ],
            'company_id' => [
                'description' => 'Company ID.',
                'example'     => 1,
            ],
        ];
    }
}
