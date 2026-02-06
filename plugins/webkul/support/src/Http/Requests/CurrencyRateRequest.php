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
        $rules = [
            'name'       => 'required|date',
            'rate'       => 'required|numeric|min:0',
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
