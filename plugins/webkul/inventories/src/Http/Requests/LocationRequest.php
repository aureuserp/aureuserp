<?php

namespace Webkul\Inventory\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Webkul\Inventory\Enums\LocationType;

class LocationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH');
        $requiredRule = $isUpdate ? ['sometimes', 'required'] : ['required'];

        return [
            'name'                       => [...$requiredRule, 'string', 'max:255'],
            'parent_id'                  => ['nullable', 'integer', 'exists:inventories_locations,id'],
            'description'                => ['nullable', 'string'],
            'type'                       => [...$requiredRule, 'string', Rule::enum(LocationType::class)],
            'company_id'                 => ['nullable', 'integer', 'exists:companies,id'],
            'storage_category_id'        => ['nullable', 'integer', 'exists:inventories_storage_categories,id'],
            'is_scrap'                   => ['nullable', 'boolean'],
            'is_dock'                    => ['nullable', 'boolean'],
            'is_replenish'               => ['nullable', 'boolean'],
            'cyclic_inventory_frequency' => ['nullable', 'integer', 'min:0'],
            'barcode'                    => ['nullable', 'string', 'max:255'],
        ];
    }

    public function bodyParameters(): array
    {
        return [
            'name' => [
                'description' => 'Location name.',
                'example'     => 'Aisle A / Shelf 1',
            ],
            'parent_id' => [
                'description' => 'Parent location ID.',
                'example'     => 5,
            ],
            'description' => [
                'description' => 'Location notes.',
                'example'     => 'Reserved for raw materials.',
            ],
            'type' => [
                'description' => 'Location type.',
                'example'     => 'internal',
            ],
            'company_id' => [
                'description' => 'Company ID.',
                'example'     => 1,
            ],
            'storage_category_id' => [
                'description' => 'Storage category ID.',
                'example'     => 3,
            ],
            'is_scrap' => [
                'description' => 'Mark as scrap location.',
                'example'     => false,
            ],
            'is_dock' => [
                'description' => 'Mark as dock location.',
                'example'     => false,
            ],
            'is_replenish' => [
                'description' => 'Mark as replenish location.',
                'example'     => true,
            ],
            'cyclic_inventory_frequency' => [
                'description' => 'Cycle counting frequency in days.',
                'example'     => 30,
            ],
            'barcode' => [
                'description' => 'Barcode for the location.',
                'example'     => 'LOC-A1',
            ],
        ];
    }
}
