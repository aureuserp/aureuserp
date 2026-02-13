<?php

namespace Webkul\Support\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CountryRequest extends FormRequest
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
            'name'           => 'required|string|max:255',
            'code'           => 'required|string|max:2',
            'phone_code'     => 'nullable|string|max:10',
            'currency_id'    => 'nullable|exists:currencies,id',
            'state_required' => 'required|boolean',
            'zip_required'   => 'required|boolean',
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
     * Get body parameters for Scribe documentation.
     */
    public function bodyParameters(): array
    {
        return [
            'name'           => [
                'description' => 'Country name',
                'example' => 'United States'
            ],
            'code'           => [
                'description' => 'ISO 3166-1 alpha-2 country code',
                'example' => 'US'
            ],
            'phone_code'     => [
                'description' => 'Phone country code',
                'example' => '+1'
            ],
            'currency_id'    => [
                'description' => 'Default currency ID',
                'example' => 1
            ],
            'state_required' => [
                'description' => 'Whether state is required',
                'example' => true
            ],
            'zip_required'   => [
                'description' => 'Whether ZIP code is required',
                'example' => true
            ],
        ];
    }
}
