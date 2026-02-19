<?php

namespace Webkul\Product\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Webkul\Product\Enums\AttributeType;

class AttributeRequest extends FormRequest
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
        $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH');

        $rules = [
            'name' => ($isUpdate ? 'sometimes|' : '').'required|string|max:255',
            'type' => ($isUpdate ? 'sometimes|' : '').'required|string|in:'.implode(',', array_column(AttributeType::cases(), 'value')),
            'sort' => 'nullable|integer|min:0',
        ];

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
                'description' => 'Attribute name (max 255 characters).',
                'example'     => 'Size',
            ],
            'type' => [
                'description' => 'Attribute type: radio, select, or color.',
                'example'     => 'select',
            ],
            'sort' => [
                'description' => 'Sort order (minimum 0).',
                'example'     => 1,
            ],
        ];
    }
}
