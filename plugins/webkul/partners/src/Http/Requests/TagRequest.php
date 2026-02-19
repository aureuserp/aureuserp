<?php

namespace Webkul\Partner\Http\Requests;

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
        $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH');

        $rules = [
            'name'  => ($isUpdate ? 'sometimes|' : '').'required|string|max:255',
            'color' => 'nullable|string|max:7',
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
                'description' => 'Tag name (max 255 characters).',
                'example'     => 'VIP Client',
            ],
            'color' => [
                'description' => 'Tag color in hex format (max 7 characters).',
                'example'     => '#FF5733',
            ],
        ];
    }
}
