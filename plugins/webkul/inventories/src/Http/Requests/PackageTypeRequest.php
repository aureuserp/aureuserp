<?php

namespace Webkul\Inventory\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PackageTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'name'                => ['required', 'string', 'max:255'],
            'length'              => ['required', 'numeric', 'min:0', 'max:99999999999'],
            'width'               => ['required', 'numeric', 'min:0', 'max:99999999999'],
            'height'              => ['required', 'numeric', 'min:0', 'max:99999999999'],
            'base_weight'         => ['required', 'numeric', 'min:0', 'max:99999999999'],
            'max_weight'          => ['required', 'numeric', 'min:0', 'max:99999999999'],
            'barcode'             => ['nullable', 'string', 'max:255'],
            'company_id'          => ['nullable', 'integer', 'exists:companies,id'],
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
                'description' => 'Package type name.',
                'example'     => 'Box L',
            ],
            'length' => [
                'description' => 'Length.',
                'example'     => 40,
            ],
            'width' => [
                'description' => 'Width.',
                'example'     => 30,
            ],
            'height' => [
                'description' => 'Height.',
                'example'     => 20,
            ],
            'base_weight' => [
                'description' => 'Base package weight.',
                'example'     => 1.5,
            ],
            'max_weight' => [
                'description' => 'Maximum package weight.',
                'example'     => 25,
            ],
            'barcode' => [
                'description' => 'Package type barcode.',
                'example'     => 'PKG-TYPE-001',
            ],
            'company_id' => [
                'description' => 'Company ID.',
                'example'     => 1,
            ],
        ];
    }
}
