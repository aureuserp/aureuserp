<?php

namespace Webkul\Product\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PackagingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'name'       => 'required|string|max:255',
            'barcode'    => 'nullable|string|max:255',
            'qty'        => 'required|numeric|min:0',
            'sort'       => 'nullable|integer',
            'product_id' => 'required|integer|exists:products_products,id',
            'company_id' => 'nullable|integer|exists:companies,id',
        ];

        // On update, make all fields optional
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules = array_map(function ($rule) {
                if (is_string($rule) && str_starts_with($rule, 'required')) {
                    return str_replace('required', 'sometimes|required', $rule);
                }

                return $rule;
            }, $rules);
        }

        return $rules;
    }

    /**
     * Get body parameters for API documentation.
     *
     * @return array<string, array<string, mixed>>
     */
    public function bodyParameters(): array
    {
        return [
            'name' => [
                'description' => 'Packaging name (max 255 characters).',
                'example'     => 'Box of 12',
            ],
            'barcode' => [
                'description' => 'Packaging barcode (max 255 characters).',
                'example'     => '5901234123457',
            ],
            'qty' => [
                'description' => 'Quantity per package (minimum 0).',
                'example'     => 12,
            ],
            'sort' => [
                'description' => 'Sort order.',
                'example'     => 1,
            ],
            'product_id' => [
                'description' => 'Product ID that this packaging belongs to.',
                'example'     => 1,
            ],
            'company_id' => [
                'description' => 'Company ID associated with this packaging.',
                'example'     => 1,
            ],
        ];
    }
}
