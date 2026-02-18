<?php

namespace Webkul\Account\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;
use Webkul\Account\Enums\AmountType;
use Webkul\Account\Enums\RepartitionType;
use Webkul\Account\Enums\TaxIncludeOverride;
use Webkul\Account\Enums\TaxScope;
use Webkul\Account\Enums\TypeTaxUse;

class TaxRequest extends FormRequest
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
            'name'                                           => ['required', 'string', 'max:255'],
            'type_tax_use'                                   => ['required', Rule::enum(TypeTaxUse::class)],
            'amount_type'                                    => ['required', Rule::enum(AmountType::class)],
            'amount'                                         => ['required', 'numeric', 'min:0'],
            'tax_group_id'                                   => ['required', 'integer', 'exists:accounts_tax_groups,id'],
            'company_id'                                     => ['nullable', 'integer', 'exists:companies,id'],
            'country_id'                                     => ['nullable', 'integer', 'exists:countries,id'],
            'tax_scope'                                      => ['nullable', Rule::enum(TaxScope::class)],
            'price_include_override'                         => ['nullable', Rule::enum(TaxIncludeOverride::class)],
            'cash_basis_transition_account_id'               => ['nullable', 'integer', 'exists:accounts_accounts,id'],
            'description'                                    => ['nullable', 'string'],
            'invoice_label'                                  => ['nullable', 'string', 'max:255'],
            'invoice_legal_notes'                            => ['nullable', 'string'],
            'tax_exigibility'                                => ['nullable', 'string', 'max:255'],
            'is_active'                                      => ['nullable', 'boolean'],
            'include_base_amount'                            => ['nullable', 'boolean'],
            'is_base_affected'                               => ['nullable', 'boolean'],
            'analytic'                                       => ['nullable', 'boolean'],
            'invoice_repartition_lines'                      => ['required', 'array', 'min:2'],
            'invoice_repartition_lines.*.id'                 => ['sometimes', 'integer', 'exists:accounts_tax_partition_lines,id'],
            'invoice_repartition_lines.*.repartition_type'   => ['required', Rule::enum(RepartitionType::class)],
            'invoice_repartition_lines.*.factor_percent'     => ['nullable', 'numeric', 'min:-100', 'max:100'],
            'invoice_repartition_lines.*.account_id'         => ['nullable', 'integer', 'exists:accounts_accounts,id'],
            'invoice_repartition_lines.*.use_in_tax_closing' => ['sometimes', 'boolean'],
            'refund_repartition_lines'                       => ['required', 'array', 'min:2'],
            'refund_repartition_lines.*.id'                  => ['sometimes', 'integer', 'exists:accounts_tax_partition_lines,id'],
            'refund_repartition_lines.*.repartition_type'    => ['required', Rule::enum(RepartitionType::class)],
            'refund_repartition_lines.*.factor_percent'      => ['nullable', 'numeric', 'min:-100', 'max:100'],
            'refund_repartition_lines.*.account_id'          => ['nullable', 'integer', 'exists:accounts_accounts,id'],
            'refund_repartition_lines.*.use_in_tax_closing'  => ['sometimes', 'boolean'],
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $this->validateRepartitionLines($validator, 'invoice_repartition_lines');
            $this->validateRepartitionLines($validator, 'refund_repartition_lines');
        });
    }

    /**
     * Validate repartition lines structure.
     */
    protected function validateRepartitionLines(Validator $validator, string $field): void
    {
        $lines = $this->input($field, []);

        if (empty($lines)) {
            return;
        }

        $baseCount = collect($lines)->where('repartition_type', RepartitionType::BASE->value)->count();
        $taxCount = collect($lines)->where('repartition_type', RepartitionType::TAX->value)->count();

        if ($baseCount !== 1) {
            $validator->errors()->add(
                $field,
                "The {$field} must have exactly 1 base line (found {$baseCount})."
            );
        }

        if ($taxCount < 1) {
            $validator->errors()->add(
                $field,
                "The {$field} must have at least 1 tax line (found {$taxCount})."
            );
        }
    }

    /**
     * Get body parameters for Scribe documentation.
     */
    public function bodyParameters(): array
    {
        return [
            'name' => [
                'description' => 'Tax name',
                'example'     => 'VAT 20%',
            ],
            'type_tax_use' => [
                'description' => 'Tax type usage',
                'example'     => TypeTaxUse::SALE->value,
            ],
            'amount_type' => [
                'description' => 'Tax computation type',
                'example'     => AmountType::PERCENT->value,
            ],
            'amount' => [
                'description' => 'Tax amount/percentage',
                'example'     => 20.00,
            ],
            'tax_group_id' => [
                'description' => 'Tax group ID',
                'example'     => 1,
            ],
            'company_id' => [
                'description' => 'Company ID',
                'example'     => 1,
            ],
            'country_id' => [
                'description' => 'Country ID',
                'example'     => 233,
            ],
            'tax_scope' => [
                'description' => 'Tax scope',
                'example'     => TaxScope::SERVICE->value,
            ],
            'price_include_override' => [
                'description' => 'Price include override',
                'example'     => TaxIncludeOverride::DEFAULT->value,
            ],
            'cash_basis_transition_account_id' => [
                'description' => 'Cash basis transition account ID',
                'example'     => 1,
            ],
            'description' => [
                'description' => 'Tax description',
                'example'     => 'Standard VAT rate',
            ],
            'invoice_label' => [
                'description' => 'Label to display on invoices',
                'example'     => 'VAT',
            ],
            'invoice_legal_notes' => [
                'description' => 'Legal notes to display on invoices',
                'example'     => 'Subject to VAT regulations',
            ],
            'tax_exigibility' => [
                'description' => 'Tax exigibility',
                'example'     => 'on_invoice',
            ],
            'is_active' => [
                'description' => 'Tax status active/inactive',
                'example'     => true,
            ],
            'include_base_amount' => [
                'description' => 'Include base amount in tax calculation',
                'example'     => false,
            ],
            'is_base_affected' => [
                'description' => 'Is base affected by other taxes',
                'example'     => false,
            ],
            'analytic' => [
                'description' => 'Enable analytic accounting',
                'example'     => false,
            ],
            'invoice_repartition_lines' => [
                'description' => 'Tax repartition lines for invoices (at least 1 base and 1 tax line required). For updates: include "id" to update existing line, omit to create new. Lines not included will be deleted.',
                'example'     => [
                    [
                        'repartition_type'    => RepartitionType::BASE->value,
                        'factor_percent'      => null,
                        'account_id'          => null,
                        'use_in_tax_closing'  => false,
                    ],
                    [
                        'repartition_type'    => RepartitionType::TAX->value,
                        'factor_percent'      => 100.00,
                        'account_id'          => 1,
                        'use_in_tax_closing'  => false,
                    ],
                ],
            ],
            'refund_repartition_lines' => [
                'description' => 'Tax repartition lines for refunds (must match invoice lines structure). For updates: include "id" to update existing line, omit to create new. Lines not included will be deleted.',
                'example'     => [
                    [
                        'repartition_type'    => RepartitionType::BASE->value,
                        'factor_percent'      => null,
                        'account_id'          => null,
                        'use_in_tax_closing'  => false,
                    ],
                    [
                        'repartition_type'    => RepartitionType::TAX->value,
                        'factor_percent'      => 100.00,
                        'account_id'          => 1,
                        'use_in_tax_closing'  => false,
                    ],
                ],
            ],
        ];
    }
}
