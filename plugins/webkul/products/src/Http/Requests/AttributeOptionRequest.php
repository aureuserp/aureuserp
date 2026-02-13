<?php

namespace Webkul\Product\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AttributeOptionRequest extends FormRequest
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
            'name'        => 'required|string|max:255',
            'color'       => 'nullable|string|max:7',
            'extra_price' => 'nullable|numeric|min:0',
            'sort'        => 'nullable|integer|min:0',
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
                'description' => 'Option name (max 255 characters).',
                'example'     => 'Large',
            ],
            'color' => [
                'description' => 'Option color in hex format (max 7 characters).',
                'example'     => '#FF5733',
            ],
            'extra_price' => [
                'description' => 'Additional price for this option (minimum 0).',
                'example'     => 10.50,
            ],
            'sort' => [
                'description' => 'Sort order (minimum 0).',
                'example'     => 1,
            ],
        ];
    }
}
