<?php

namespace Webkul\PointOfSale\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Webkul\Inventory\Enums\ProductTracking;

class TerminalProductRequest extends FormRequest
{
    public function rules(): array
    {
        $companyId = $this->route('config')?->company_id;

        return [
            'name'        => ['required', 'string', 'max:255'],
            'barcode'     => ['nullable', 'string', 'max:64', Rule::unique('products_products', 'barcode')
                ->whereNull('deleted_at')
                ->where(fn ($query) => $query
                    ->whereNull('company_id')
                    ->orWhere('company_id', $companyId))],
            'price'       => ['nullable', 'numeric', 'min:0'],
            'category_id' => ['nullable', 'integer', 'exists:pos_categories,id'],
            'is_storable' => ['nullable', 'boolean'],
            'tracking'    => ['nullable', Rule::enum(ProductTracking::class)],
            'tax_ids'     => ['nullable', 'array'],
            'tax_ids.*'   => ['integer', 'exists:accounts_taxes,id'],
        ];
    }
}
