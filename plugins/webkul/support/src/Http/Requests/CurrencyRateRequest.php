<?php

namespace Webkul\Support\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CurrencyRateRequest extends FormRequest
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
            'name'       => [($isUpdate ? 'sometimes|required' : 'required'), 'date'],
            'rate'       => [($isUpdate ? 'sometimes|required' : 'required'), 'numeric', 'min:0'],
            'company_id' => ['nullable', 'integer', 'exists:companies,id'],
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
                'description' => 'Rate effective date (YYYY-MM-DD format).',
                'example'     => '2024-01-15',
            ],
            'rate' => [
                'description' => 'Exchange rate (minimum 0).',
                'example'     => 1.25,
            ],
            'company_id' => [
                'description' => 'Company ID (optional, null for global rates).',
                'example'     => 1,
            ],
        ];
    }
}
