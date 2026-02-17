<?php

namespace Webkul\Account\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaxGroupRequest extends FormRequest
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
        return [
            'name'               => ['required', 'string', 'max:255'],
            'company_id'         => ['nullable', 'integer', 'exists:companies,id'],
            'country_id'         => ['nullable', 'integer', 'exists:countries,id'],
            'preceding_subtotal' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Get body parameters for Scribe documentation.
     */
    public function bodyParameters(): array
    {
        return [
            'name' => [
                'description' => 'Tax group name',
                'example'     => 'VAT Group',
            ],
            'company_id' => [
                'description' => 'Company ID',
                'example'     => 1,
            ],
            'country_id' => [
                'description' => 'Country ID',
                'example'     => 233,
            ],
            'preceding_subtotal' => [
                'description' => 'Preceding subtotal label',
                'example'     => 'Subtotal',
            ],
        ];
    }
}
