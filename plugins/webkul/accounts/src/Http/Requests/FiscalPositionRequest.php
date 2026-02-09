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

            // Tax mappings
            'taxes'                          => ['nullable', 'array'],
            'taxes.*.id'                     => ['sometimes', 'integer', 'exists:accounts_fiscal_position_taxes,id'],
            'taxes.*.tax_source_id'          => ['required', 'integer', 'exists:accounts_taxes,id'],
            'taxes.*.tax_destination_id'     => ['nullable', 'integer', 'exists:accounts_taxes,id'],

            // Account mappings
            'accounts'                          => ['nullable', 'array'],
            'accounts.*.id'                     => ['sometimes', 'integer', 'exists:accounts_fiscal_position_accounts,id'],
            'accounts.*.account_source_id'      => ['required', 'integer', 'exists:accounts_accounts,id'],
            'accounts.*.account_destination_id' => ['required', 'integer', 'exists:accounts_accounts,id'],
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
            'taxes' => [
                'description' => 'Tax mappings array. Include id for update, omit for create.',
                'example'     => [
                    [
                        'tax_source_id'      => 1,
                        'tax_destination_id' => 2,
                    ],
                ],
            ],
            'accounts' => [
                'description' => 'Account mappings array. Include id for update, omit for create.',
                'example'     => [
                    [
                        'account_source_id'      => 5,
                        'account_destination_id' => 6,
                    ],
                ],
            ],
        ];
    }
}
