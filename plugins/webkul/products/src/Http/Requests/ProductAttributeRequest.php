<?php

namespace Webkul\Product\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductAttributeRequest extends FormRequest
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
            'attribute_id' => 'required|integer|exists:products_attributes,id',
            'sort'         => 'nullable|integer|min:0',
            'options'      => 'nullable|array',
            'options.*'    => 'integer|exists:products_attribute_options,id',
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
            'attribute_id' => [
                'description' => 'The ID of the attribute to link to this product.',
                'example'     => 1,
            ],
            'sort' => [
                'description' => 'Sort order (minimum 0).',
                'example'     => 0,
            ],
            'options' => [
                'description' => 'Array of attribute option IDs to associate with this product attribute.',
                'example'     => [1, 2, 3],
            ],
        ];
    }
}
