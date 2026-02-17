<?php

namespace Webkul\Account\Http\Requests;

class BillRequest extends InvoiceRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = parent::rules();

        $rules['date'] = 'nullable|date';

        return $rules;
    }

    /**
     * Get body parameters for API documentation.
     *
     * @return array<string, array<string, mixed>>
     */
    public function bodyParameters(): array
    {
        $parameters = parent::bodyParameters();

        $parameters['partner_id']['description'] = 'The vendor/partner ID.';
        $parameters['journal_id']['description'] = 'The journal ID for bills.';
        $parameters['reference']['description'] = 'The vendor bill reference.';
        $parameters['date'] = [
            'description' => 'The accounting date.',
            'example'     => '2026-02-17',
        ];

        return $parameters;
    }
}
