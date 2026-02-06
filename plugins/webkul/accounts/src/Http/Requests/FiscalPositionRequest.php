<?php

namespace Webkul\Account\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FiscalPositionRequest extends FormRequest
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
            'name'             => ['required', 'string', 'max:255'],
            'company_id'       => ['required', 'integer', 'exists:companies,id'],
            'country_id'       => ['nullable', 'integer', 'exists:countries,id'],
            'country_group_id' => ['nullable', 'integer', 'exists:countries,id'],
            'zip_from'         => ['nullable', 'string', 'max:10'],
            'zip_to'           => ['nullable', 'string', 'max:10'],
            'notes'            => ['nullable', 'string'],
            'auto_reply'       => ['nullable', 'boolean'],
            'vat_required'     => ['nullable', 'boolean'],
            'foreign_vat'      => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Get body parameters for Scribe documentation.
     */
    public function bodyParameters(): array
    {
        return [
            'name' => [
                'description' => 'Fiscal position name',
                'example'     => 'Domestic',
            ],
            'company_id' => [
                'description' => 'Company ID',
                'example'     => 1,
            ],
            'country_id' => [
                'description' => 'Country ID',
                'example'     => 233,
            ],
            'country_group_id' => [
                'description' => 'Country group ID',
                'example'     => null,
            ],
            'zip_from' => [
                'description' => 'ZIP code from',
                'example'     => '10000',
            ],
            'zip_to' => [
                'description' => 'ZIP code to',
                'example'     => '99999',
            ],
            'notes' => [
                'description' => 'Fiscal position notes',
                'example'     => 'For domestic transactions',
            ],
            'auto_reply' => [
                'description' => 'Auto reply enabled',
                'example'     => false,
            ],
            'vat_required' => [
                'description' => 'VAT required flag',
                'example'     => true,
            ],
            'foreign_vat' => [
                'description' => 'Foreign VAT',
                'example'     => null,
            ],
        ];
    }
}
