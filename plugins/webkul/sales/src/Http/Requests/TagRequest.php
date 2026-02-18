<?php

namespace Webkul\Sale\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TagRequest extends FormRequest
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
        $tagId = $this->route('tag');

        $rules = [
            'name'  => 'required|string|max:255|unique:sales_tags,name'.($tagId ? ','.$tagId : ''),
            'color' => 'nullable|string|max:7',
        ];

        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            foreach ($rules as $key => $rule) {
                if (is_string($rule) && str_starts_with($rule, 'required')) {
                    $rules[$key] = str_replace('required', 'sometimes|required', $rule);
                }
            }
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
                'description' => 'Sales tag name (max 255 characters).',
                'example'     => 'Urgent',
            ],
            'color' => [
                'description' => 'Tag color in hex format (max 7 characters).',
                'example'     => '#FF5733',
            ],
        ];
    }
}
