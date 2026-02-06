<?php

namespace Webkul\Product\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductAttributeValueRequest extends FormRequest
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
            'extra_price'          => 'nullable|numeric|min:0',
            'attribute_id'         => 'required|integer|exists:products_attributes,id',
            'product_attribute_id' => 'required|integer|exists:products_product_attributes,id',
            'attribute_option_id'  => 'required|integer|exists:products_attribute_options,id',
        ];

        // On update, make fields optional
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
            'extra_price' => [
                'description' => 'Additional price for this attribute value (minimum 0).',
                'example'     => 10.50,
            ],
            'attribute_id' => [
                'description' => 'The ID of the attribute.',
                'example'     => 1,
            ],
            'product_attribute_id' => [
                'description' => 'The ID of the product attribute.',
                'example'     => 1,
            ],
            'attribute_option_id' => [
                'description' => 'The ID of the attribute option.',
                'example'     => 1,
            ],
        ];
    }
}
