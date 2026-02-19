<?php

namespace Webkul\Inventory\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Webkul\Inventory\Enums\DeliveryStep;
use Webkul\Inventory\Enums\ReceptionStep;

class WarehouseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $warehouseId = $this->route('warehouse') ?? $this->route('id');

        $rules = [
            'name'                  => ['required', 'string', 'max:255', Rule::unique('inventories_warehouses', 'name')->ignore($warehouseId)],
            'code'                  => ['required', 'string', 'max:255', Rule::unique('inventories_warehouses', 'code')->ignore($warehouseId)],
            'company_id'            => ['required', 'integer', 'exists:companies,id'],
            'partner_address_id'    => ['nullable', 'integer', 'exists:partners_partners,id'],
            'reception_steps'       => ['nullable', Rule::enum(ReceptionStep::class)],
            'delivery_steps'        => ['nullable', Rule::enum(DeliveryStep::class)],
            'supplier_warehouses'   => ['nullable', 'array'],
            'supplier_warehouses.*' => ['integer', 'exists:inventories_warehouses,id'],
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
                'description' => 'Warehouse name.',
                'example'     => 'Main Warehouse',
            ],
            'code' => [
                'description' => 'Warehouse code.',
                'example'     => 'WH-MAIN',
            ],
            'company_id' => [
                'description' => 'Company ID.',
                'example'     => 1,
            ],
            'partner_address_id' => [
                'description' => 'Partner address ID.',
                'example'     => 10,
            ],
            'reception_steps' => [
                'description' => 'Incoming shipment workflow step value.',
                'example'     => 'one_step',
            ],
            'delivery_steps' => [
                'description' => 'Outgoing shipment workflow step value.',
                'example'     => 'one_step',
            ],
            'supplier_warehouses' => [
                'description' => 'Supplier warehouse IDs.',
                'example'     => [2, 3],
            ],
        ];
    }
}
