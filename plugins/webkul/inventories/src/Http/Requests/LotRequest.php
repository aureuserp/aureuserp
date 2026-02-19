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
        $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH');

        return [
            'name'        => [($isUpdate ? 'sometimes|required' : 'required'), 'string', 'max:255'],
            'product_id'  => [($isUpdate ? 'sometimes|required' : 'required'), 'integer', 'exists:products_products,id'],
            'reference'   => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ];
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
