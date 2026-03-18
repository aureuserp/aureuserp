<?php

namespace Webkul\Inventory\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Webkul\Inventory\Enums\ScrapState;

class ScrapRequest extends FormRequest
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
            'origin'                  => ['nullable', 'string', 'max:255'],
            'qty'                     => [...$requiredRule, 'numeric', 'min:1', 'max:99999999999'],
            'product_id'              => [...$requiredRule, 'integer', 'exists:products_products,id'],
            'uom_id'                  => ['nullable', 'integer', 'exists:unit_of_measures,id'],
            'lot_id'                  => ['nullable', 'integer', 'exists:inventories_lots,id'],
            'package_id'              => ['nullable', 'integer', 'exists:inventories_packages,id'],
            'partner_id'              => ['nullable', 'integer', 'exists:partners_partners,id'],
            'source_location_id'      => ['nullable', 'integer', 'exists:inventories_locations,id'],
            'destination_location_id' => ['nullable', 'integer', 'exists:inventories_locations,id'],
            'company_id'              => ['nullable', 'integer', 'exists:companies,id'],
            'tags'                    => ['nullable', 'array'],
            'tags.*'                  => ['integer', 'exists:inventories_tags,id'],
        ];
    }

    public function bodyParameters(): array
    {
        return [
            'origin' => [
                'description' => 'Source document reference.',
                'example'     => 'WH/OUT/0001',
            ],
            'qty' => [
                'description' => 'Quantity to scrap.',
                'example'     => 2,
            ],
            'product_id' => [
                'description' => 'Product ID.',
                'example'     => 1,
            ],
            'uom_id' => [
                'description' => 'Unit of measure ID.',
                'example'     => 1,
            ],
            'lot_id' => [
                'description' => 'Lot ID.',
                'example'     => 1,
            ],
            'package_id' => [
                'description' => 'Package ID.',
                'example'     => 1,
            ],
            'partner_id' => [
                'description' => 'Owner partner ID.',
                'example'     => 1,
            ],
            'source_location_id' => [
                'description' => 'Source location ID.',
                'example'     => 1,
            ],
            'destination_location_id' => [
                'description' => 'Destination scrap location ID.',
                'example'     => 2,
            ],
            'company_id' => [
                'description' => 'Company ID.',
                'example'     => 1,
            ],
            'tags' => [
                'description' => 'Tag IDs to attach to this scrap.',
                'example'     => [1, 2],
            ],
        ];
    }
}
