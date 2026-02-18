<?php

namespace Webkul\Account\Http\Requests;

use Webkul\Partner\Http\Requests\PartnerRequest as BasePartnerRequest;

class CustomerRequest extends BasePartnerRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = parent::rules();

        // Add account-specific validation rules
        $additionalRules = [
            'autopost_bills'                              => ['nullable', 'boolean'],
            'ignore_abnormal_invoice_date'                => ['nullable', 'boolean'],
            'ignore_abnormal_invoice_amount'              => ['nullable', 'numeric'],
            'invoice_sending_method'                      => ['nullable', 'string', 'max:255'],
            'invoice_edi_format_store'                    => ['nullable', 'string', 'max:255'],
            'peppol_endpoint'                             => ['nullable', 'string', 'max:255'],
            'peppol_eas'                                  => ['nullable', 'string', 'max:255'],
            'comment'                                     => ['nullable', 'string'],
            'property_account_payable_id'                 => ['nullable', 'integer', 'exists:accounts_accounts,id'],
            'property_account_receivable_id'              => ['nullable', 'integer', 'exists:accounts_accounts,id'],
            'property_account_position_id'                => ['nullable', 'integer', 'exists:accounts_fiscal_positions,id'],
            'property_payment_term_id'                    => ['nullable', 'integer', 'exists:accounts_payment_terms,id'],
            'property_supplier_payment_term_id'           => ['nullable', 'integer', 'exists:accounts_payment_terms,id'],
            'property_outbound_payment_method_line_id'    => ['nullable', 'integer', 'exists:accounts_payment_method_lines,id'],
            'property_inbound_payment_method_line_id'     => ['nullable', 'integer', 'exists:accounts_payment_method_lines,id'],
        ];

        return array_merge($rules, $additionalRules);
    }

    /**
     * Get body parameters for API documentation.
     *
     * @return array<string, array<string, mixed>>
     */
    public function bodyParameters(): array
    {
        $params = parent::bodyParameters();

        $additionalParams = [
            'autopost_bills' => [
                'description' => 'Auto-post bills flag',
                'example'     => false,
            ],
            'ignore_abnormal_invoice_date' => [
                'description' => 'Ignore abnormal invoice date flag',
                'example'     => false,
            ],
            'ignore_abnormal_invoice_amount' => [
                'description' => 'Ignore abnormal invoice amount threshold',
                'example'     => 1000.00,
            ],
            'invoice_sending_method' => [
                'description' => 'Invoice sending method',
                'example'     => 'email',
            ],
            'invoice_edi_format_store' => [
                'description' => 'Invoice EDI format store',
                'example'     => null,
            ],
            'peppol_endpoint' => [
                'description' => 'PEPPOL endpoint',
                'example'     => null,
            ],
            'peppol_eas' => [
                'description' => 'PEPPOL EAS',
                'example'     => null,
            ],
            'comment' => [
                'description' => 'Additional comments',
                'example'     => null,
            ],
            'property_account_payable_id' => [
                'description' => 'Property account payable ID',
                'example'     => 1,
            ],
            'property_account_receivable_id' => [
                'description' => 'Property account receivable ID',
                'example'     => 2,
            ],
            'property_account_position_id' => [
                'description' => 'Property account position (fiscal position) ID',
                'example'     => 1,
            ],
            'property_payment_term_id' => [
                'description' => 'Property payment term ID for customers',
                'example'     => 1,
            ],
            'property_supplier_payment_term_id' => [
                'description' => 'Property payment term ID for suppliers',
                'example'     => 2,
            ],
            'property_outbound_payment_method_line_id' => [
                'description' => 'Property outbound payment method line ID',
                'example'     => 1,
            ],
            'property_inbound_payment_method_line_id' => [
                'description' => 'Property inbound payment method line ID',
                'example'     => 2,
            ],
        ];

        return array_merge($params, $additionalParams);
    }
}
