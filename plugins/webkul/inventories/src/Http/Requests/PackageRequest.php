<?php

namespace Webkul\Inventory\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PackageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'name'            => ['required', 'string', 'max:255'],
            'package_type_id' => ['nullable', 'integer', 'exists:inventories_package_types,id'],
            'pack_date'       => ['nullable', 'date'],
            'location_id'     => ['nullable', 'integer', 'exists:inventories_locations,id'],
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
                'description' => 'Package name/reference.',
                'example'     => 'PACK-0001',
            ],
            'package_type_id' => [
                'description' => 'Package type ID.',
                'example'     => 1,
            ],
            'pack_date' => [
                'description' => 'Pack date.',
                'example'     => '2026-02-19',
            ],
            'location_id' => [
                'description' => 'Current location ID.',
                'example'     => 3,
            ],
        ];
    }
}
