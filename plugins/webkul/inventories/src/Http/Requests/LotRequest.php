<?php

namespace Webkul\Inventory\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LotRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'name'        => ['required', 'string', 'max:255'],
            'product_id'  => ['required', 'integer', 'exists:products_products,id'],
            'reference'   => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
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
                'description' => 'Lot/serial number.',
                'example'     => 'LOT-2026-001',
            ],
            'product_id' => [
                'description' => 'Tracked product ID.',
                'example'     => 1,
            ],
            'reference' => [
                'description' => 'Internal lot reference.',
                'example'     => 'BATCH-A1',
            ],
            'description' => [
                'description' => 'Lot description.',
                'example'     => 'Primary production batch.',
            ],
        ];
    }
}
