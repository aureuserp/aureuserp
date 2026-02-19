<?php

namespace Webkul\Inventory\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Webkul\Inventory\Enums\AllowNewProduct;

class StorageCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH');

        return [
            'name'               => ($isUpdate ? 'sometimes|' : '').'required|string|max:255',
            'max_weight'         => 'nullable|numeric|min:0|max:99999999',
            'allow_new_products' => ($isUpdate ? 'sometimes|' : '').'required|string|in:'.implode(',', array_column(AllowNewProduct::cases(), 'value')),
            'company_id'         => 'nullable|integer|exists:companies,id',
        ];
    }

    public function bodyParameters(): array
    {
        return [
            'name' => [
                'description' => 'Storage category name.',
                'example'     => 'Heavy Goods',
            ],
            'max_weight' => [
                'description' => 'Maximum allowed weight.',
                'example'     => 500.0,
            ],
            'allow_new_products' => [
                'description' => 'Policy for new products.',
                'example'     => 'mixed',
            ],
            'company_id' => [
                'description' => 'Company ID.',
                'example'     => 1,
            ],
        ];
    }
}
