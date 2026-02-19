<?php

namespace Webkul\Inventory\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RouteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'name'                      => ['required', 'string', 'max:255'],
            'company_id'                => ['nullable', 'integer', 'exists:companies,id'],
            'product_category_selectable' => ['nullable', 'boolean'],
            'product_selectable'        => ['nullable', 'boolean'],
            'packaging_selectable'      => ['nullable', 'boolean'],
            'warehouse_selectable'      => ['nullable', 'boolean'],
            'warehouses'                => ['nullable', 'array'],
            'warehouses.*'              => ['integer', 'exists:inventories_warehouses,id'],
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
                'description' => 'Route name.',
                'example'     => 'MTO: Buy',
            ],
            'company_id' => [
                'description' => 'Company ID.',
                'example'     => 1,
            ],
            'product_category_selectable' => [
                'description' => 'Whether this route can be applied to product categories.',
                'example'     => true,
            ],
            'product_selectable' => [
                'description' => 'Whether this route can be applied to products.',
                'example'     => true,
            ],
            'packaging_selectable' => [
                'description' => 'Whether this route can be applied to packaging.',
                'example'     => false,
            ],
            'warehouse_selectable' => [
                'description' => 'Whether this route can be applied to warehouses.',
                'example'     => true,
            ],
            'warehouses' => [
                'description' => 'Warehouse IDs when warehouse selection is enabled.',
                'example'     => [1, 2],
            ],
        ];
    }
}
