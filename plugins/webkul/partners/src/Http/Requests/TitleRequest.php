<?php

namespace Webkul\Partner\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TitleRequest extends FormRequest
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
            'name'       => ($isUpdate ? 'sometimes|' : '').'required|string|max:255',
            'short_name' => 'nullable|string|max:50',
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
                'description' => 'Title name (max 255 characters).',
                'example'     => 'Mr.',
            ],
            'short_name' => [
                'description' => 'Short name for the title (max 50 characters).',
                'example'     => 'Mr',
            ],
        ];
    }
}
