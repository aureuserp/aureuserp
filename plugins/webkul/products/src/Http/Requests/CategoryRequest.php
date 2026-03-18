<?php

namespace Webkul\Product\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
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
        $requiredRule = $isUpdate ? ['sometimes', 'required'] : ['required'];

        $rules = [
            'name'      => [...$requiredRule, 'string', 'max:255'],
            'parent_id' => ['nullable', 'integer', 'exists:products_categories,id'],
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
                'description' => 'Category name (max 255 characters).',
                'example'     => 'Electronics',
            ],
            'parent_id' => [
                'description' => 'Parent category ID for nested categories.',
                'example'     => 1,
            ],
        ];
    }
}
