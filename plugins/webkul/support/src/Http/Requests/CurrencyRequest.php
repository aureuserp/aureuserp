<?php

namespace Webkul\Support\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CurrencyRequest extends FormRequest
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
        $currencyId = $this->route('currency') ?? $this->route('id');

        $rules = [
            'name'           => 'required|string|max:255|unique:currencies,name'.($currencyId ? ','.$currencyId : ''),
            'symbol'         => 'required|string|max:10',
            'iso_numeric'    => 'nullable|string|max:3',
            'decimal_places' => 'required|integer|min:0|max:10',
            'full_name'      => 'nullable|string|max:255',
            'rounding'       => 'nullable|numeric|min:0',
            'active'         => 'required|boolean',
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
                'description' => 'Currency name (max 255 characters).',
                'example'     => 'USD',
            ],
            'symbol' => [
                'description' => 'Currency symbol (max 10 characters).',
                'example'     => '$',
            ],
            'iso_numeric' => [
                'description' => 'ISO numeric code (max 3 characters).',
                'example'     => '840',
            ],
            'decimal_places' => [
                'description' => 'Number of decimal places (0-10).',
                'example'     => 2,
            ],
            'full_name' => [
                'description' => 'Full currency name (max 255 characters).',
                'example'     => 'United States Dollar',
            ],
            'rounding' => [
                'description' => 'Rounding precision (minimum 0).',
                'example'     => 0.01,
            ],
            'active' => [
                'description' => 'Whether the currency is active.',
                'example'     => true,
            ],
        ];
    }
}
