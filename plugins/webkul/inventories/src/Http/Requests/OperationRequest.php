<?php

namespace Webkul\Inventory\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Webkul\Inventory\Enums\MoveType;
use Webkul\Inventory\Enums\OperationType as OperationTypeEnum;
use Webkul\Inventory\Models\OperationType;
use Webkul\Product\Models\Product;

class OperationRequest extends FormRequest
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
            'partner_id'                   => ['nullable', 'integer', 'exists:partners_partners,id'],
            'operation_type_id'            => ['nullable', 'integer', 'exists:inventories_operation_types,id'],
            'source_location_id'           => ['nullable', 'integer', 'exists:inventories_locations,id'],
            'destination_location_id'      => ['nullable', 'integer', 'exists:inventories_locations,id'],
            'user_id'                      => ['nullable', 'integer', 'exists:users,id'],
            'move_type'                    => ['nullable', Rule::enum(MoveType::class)],
            'scheduled_at'                 => ['nullable', 'date'],
            'origin'                       => ['nullable', 'string', 'max:255'],
            'description'                  => ['nullable', 'string'],
            'moves'                        => ['nullable', 'array'],
            'moves.*.id'                   => ['nullable', 'integer', 'exists:inventories_moves,id'],
            'moves.*.product_id'           => [...$requiredRule, 'integer', 'exists:products_products,id'],
            'moves.*.product_uom_qty'      => [...$requiredRule, 'numeric', 'min:0', 'max:99999999999'],
            'moves.*.uom_id'               => ['nullable', 'integer', 'exists:unit_of_measures,id'],
            'moves.*.final_location_id'    => ['nullable', 'integer', 'exists:inventories_locations,id'],
            'moves.*.description_picking'  => ['nullable', 'string', 'max:255'],
            'moves.*.scheduled_at'         => ['nullable', 'date'],
            'moves.*.deadline'             => ['nullable', 'date'],
            'moves.*.product_packaging_id' => ['nullable', 'integer', 'exists:products_packagings,id'],
            'moves.*.quantity'             => ['nullable', 'numeric', 'min:0', 'max:99999999999'],
            'moves.*.is_picked'            => ['nullable', 'boolean'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            $moves = $this->input('moves', []);
            $productIds = collect($moves)->pluck('product_id')->filter()->unique();

            if ($productIds->isNotEmpty()) {
                $configurableProducts = Product::query()
                    ->whereIn('id', $productIds)
                    ->where('is_configurable', true)
                    ->get(['id', 'name'])
                    ->keyBy('id');

                if ($configurableProducts->isNotEmpty()) {
                    foreach ($moves as $index => $move) {
                        if (isset($move['product_id']) && isset($configurableProducts[$move['product_id']])) {
                            $product = $configurableProducts[$move['product_id']];

                            $validator->errors()->add(
                                "moves.{$index}.product_id",
                                "The product '{$product->name}' is configurable and cannot be used in operations. Please select a product variant instead."
                            );
                        }
                    }
                }
            }

            $requiredType = $this->operationType();

            if (! $requiredType) {
                return;
            }

            $operationTypeId = $this->input('operation_type_id');

            if (! $operationTypeId) {
                return;
            }

            $operationType = OperationType::query()->find($operationTypeId);

            if (! $operationType || $operationType->type !== $requiredType) {
                $validator->errors()->add(
                    'operation_type_id',
                    'The selected operation type does not match this resource.'
                );
            }
        });
    }

    public function bodyParameters(): array
    {
        return [
            'partner_id' => [
                'description' => 'Partner ID (receive from, contact, or delivery address based on operation type).',
                'example'     => 1,
            ],
            'operation_type_id' => [
                'description' => 'Operation type ID. If omitted, the API resolves the default operation type for the resource.',
                'example'     => 1,
            ],
            'source_location_id' => [
                'description' => 'Source location ID.',
                'example'     => 1,
            ],
            'destination_location_id' => [
                'description' => 'Destination location ID.',
                'example'     => 2,
            ],
            'user_id' => [
                'description' => 'Responsible user ID.',
                'example'     => 1,
            ],
            'move_type' => [
                'description' => 'Shipping policy.',
                'example'     => 'direct',
            ],
            'scheduled_at' => [
                'description' => 'Scheduled datetime.',
                'example'     => '2026-02-25 10:00:00',
            ],
            'origin' => [
                'description' => 'Source document reference.',
                'example'     => 'SO/00001',
            ],
            'description' => [
                'description' => 'Operation note.',
                'example'     => 'Handle with care.',
            ],
            'moves' => [
                'description' => 'Optional operation moves payload.',
                'example'     => [
                    [
                        'product_id'      => 1,
                        'product_uom_qty' => 5,
                        'uom_id'          => 1,
                        'quantity'        => 0,
                        'is_picked'       => false,
                    ],
                ],
            ],
        ];
    }

    protected function operationType(): ?OperationTypeEnum
    {
        $routeName = (string) $this->route()?->getName();

        return match (true) {
            str_contains($routeName, '.receipts.')           => OperationTypeEnum::INCOMING,
            str_contains($routeName, '.deliveries.')         => OperationTypeEnum::OUTGOING,
            str_contains($routeName, '.internal-transfers.') => OperationTypeEnum::INTERNAL,
            str_contains($routeName, '.dropships.')          => OperationTypeEnum::DROPSHIP,
            default                                          => null,
        };
    }
}
