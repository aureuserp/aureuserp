<?php

namespace Webkul\Support\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BankRequest extends FormRequest
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
            'name'       => 'required|string|max:255',
            'code'       => 'nullable|string|max:50',
            'email'      => 'nullable|email|max:255',
            'phone'      => 'nullable|string|max:50',
            'street1'    => 'nullable|string|max:255',
            'street2'    => 'nullable|string|max:255',
            'city'       => 'nullable|string|max:100',
            'zip'        => 'nullable|string|max:20',
            'state_id'   => 'nullable|exists:states,id',
            'country_id' => 'nullable|exists:countries,id',
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
            'name'       => [
                'description' => 'Bank name',
                'example' => 'Chase Bank'
            ],
            'code'       => [
                'description' => 'Bank code',
                'example' => 'CHASE'
            ],
            'email'      => [
                'description' => 'Bank email address',
                'example' => 'info@chase.com'
            ],
            'phone'      => [
                'description' => 'Bank phone number',
                'example' => '+1234567890'
            ],
            'street1'    => [
                'description' => 'Street address line 1',
                'example' => '123 Main St'
            ],
            'street2'    => [
                'description' => 'Street address line 2',
                'example' => 'Suite 100'
            ],
            'city'       => [
                'description' => 'City',
                'example' => 'New York'
            ],
            'zip'        => [
                'description' => 'ZIP/Postal code',
                'example' => '10001'
            ],
            'state_id'   => [
                'description' => 'State ID',
                'example' => 9
            ],
            'country_id' => [
                'description' => 'Country ID',
                'example' => 233
            ],
        ];
    }
}
