<?php

namespace Webkul\Partner\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Webkul\Partner\Enums\AddressType;

class AddressRequest extends FormRequest
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
            'sub_type'   => 'required|string|in:'.implode(',', array_column(AddressType::cases(), 'value')),
            'name'       => 'required|string|max:255',
            'email'      => 'nullable|email|max:255',
            'phone'      => 'nullable|string|max:20',
            'mobile'     => 'nullable|string|max:20',
            'street1'    => 'nullable|string|max:255',
            'street2'    => 'nullable|string|max:255',
            'city'       => 'nullable|string|max:255',
            'zip'        => 'nullable|string|max:20',
            'state_id'   => 'nullable|integer|exists:states,id',
            'country_id' => 'nullable|integer|exists:countries,id',
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
            'sub_type' => [
                'description' => 'Address type',
                'example'     => AddressType::INVOICE->value,
            ],
            'name' => [
                'description' => 'Address name or label (max 255 characters).',
                'example'     => 'Main Office',
            ],
            'email' => [
                'description' => 'Email address (max 255 characters).',
                'example'     => 'office@company.com',
            ],
            'phone' => [
                'description' => 'Phone number (max 20 characters).',
                'example'     => '+1234567890',
            ],
            'mobile' => [
                'description' => 'Mobile number (max 20 characters).',
                'example'     => '+1234567890',
            ],
            'street1' => [
                'description' => 'Street address line 1 (max 255 characters).',
                'example'     => '123 Main Street',
            ],
            'street2' => [
                'description' => 'Street address line 2 (max 255 characters).',
                'example'     => 'Suite 100',
            ],
            'city' => [
                'description' => 'City (max 255 characters).',
                'example'     => 'New York',
            ],
            'zip' => [
                'description' => 'Postal/ZIP code (max 20 characters).',
                'example'     => '10001',
            ],
            'state_id' => [
                'description' => 'State ID.',
                'example'     => 9,
            ],
            'country_id' => [
                'description' => 'Country ID.',
                'example'     => 233,
            ],
        ];
    }
}
