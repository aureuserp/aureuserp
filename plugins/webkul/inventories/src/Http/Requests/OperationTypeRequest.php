<?php

namespace Webkul\Inventory\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Webkul\Inventory\Enums\CreateBackorder;
use Webkul\Inventory\Enums\MoveType;
use Webkul\Inventory\Enums\OperationType as InventoryOperationType;
use Webkul\Inventory\Enums\ReservationMethod;

class OperationTypeRequest extends FormRequest
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
            'name'                               => [...$requiredRule, 'string', 'max:255'],
            'type'                               => [...$requiredRule, 'string', Rule::enum(InventoryOperationType::class)],
            'sequence_code'                      => [...$requiredRule, 'string', 'max:255'],
            'print_label'                        => ['nullable', 'boolean'],
            'warehouse_id'                       => ['nullable', 'integer', 'exists:inventories_warehouses,id'],
            'reservation_method'                 => ['nullable', 'string', Rule::enum(ReservationMethod::class)],
            'auto_show_reception_report'         => ['nullable', 'boolean'],
            'company_id'                         => ['nullable', 'integer', 'exists:companies,id'],
            'return_operation_type_id'           => ['nullable', 'integer', 'exists:inventories_operation_types,id'],
            'create_backorder'                   => [...$requiredRule, 'string', Rule::enum(CreateBackorder::class)],
            'move_type'                          => ['nullable', 'string', Rule::enum(MoveType::class)],
            'use_create_lots'                    => ['nullable', 'boolean'],
            'use_existing_lots'                  => ['nullable', 'boolean'],
            'source_location_id'                 => [...$requiredRule, 'integer', 'exists:inventories_locations,id'],
            'destination_location_id'            => [...$requiredRule, 'integer', 'exists:inventories_locations,id'],
        ];
    }

    public function bodyParameters(): array
    {
        return [
            'name' => [
                'description' => 'Operation type name.',
                'example'     => 'Receipts',
            ],
            'type' => [
                'description' => 'Operation type value.',
                'example'     => 'incoming',
            ],
            'sequence_code' => [
                'description' => 'Sequence prefix.',
                'example'     => 'IN',
            ],
            'print_label' => [
                'description' => 'Generate shipping labels.',
                'example'     => false,
            ],
            'warehouse_id' => [
                'description' => 'Warehouse ID.',
                'example'     => 1,
            ],
            'reservation_method' => [
                'description' => 'Reservation method.',
                'example'     => 'at_confirm',
            ],
            'auto_show_reception_report' => [
                'description' => 'Auto show reception report.',
                'example'     => false,
            ],
            'company_id' => [
                'description' => 'Company ID.',
                'example'     => 1,
            ],
            'return_operation_type_id' => [
                'description' => 'Return operation type ID.',
                'example'     => 2,
            ],
            'create_backorder' => [
                'description' => 'Backorder policy.',
                'example'     => 'ask',
            ],
            'move_type' => [
                'description' => 'Shipping policy.',
                'example'     => 'direct',
            ],
            'use_create_lots' => [
                'description' => 'Allow creating new lots.',
                'example'     => true,
            ],
            'use_existing_lots' => [
                'description' => 'Allow selecting existing lots.',
                'example'     => true,
            ],
            'source_location_id' => [
                'description' => 'Source location ID.',
                'example'     => 1,
            ],
            'destination_location_id' => [
                'description' => 'Destination location ID.',
                'example'     => 2,
            ],
        ];
    }
}
