<?php

namespace Webkul\Inventory\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WarehouseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH');
        $requiredRule = $isUpdate ? ['sometimes', 'required'] : ['required'];
        $warehouseId = $this->route('warehouse') ?? $this->route('id');

        return [
            'name'                  => [...$requiredRule, 'string', 'max:255', 'unique:inventories_warehouses,name'.($warehouseId ? ','.$warehouseId : '')],
            'code'                  => [...$requiredRule, 'string', 'max:255', 'unique:inventories_warehouses,code'.($warehouseId ? ','.$warehouseId : '')],
            'company_id'            => [...$requiredRule, 'integer', 'exists:companies,id'],
            'partner_address_id'    => ['nullable', 'integer', 'exists:partners_partners,id'],
            'reception_steps'       => ['nullable', 'string'],
            'delivery_steps'        => ['nullable', 'string'],
            'supplier_warehouses'   => ['nullable', 'array'],
            'supplier_warehouses.*' => ['integer', 'exists:inventories_warehouses,id'],
        ];
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
