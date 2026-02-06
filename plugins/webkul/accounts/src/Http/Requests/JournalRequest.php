<?php

namespace Webkul\Account\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Webkul\Account\Enums\JournalType;

class JournalRequest extends FormRequest
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
            'name'                      => ['required', 'string', 'max:255'],
            'code'                      => ['required', 'string', 'max:5'],
            'type'                      => ['required', Rule::enum(JournalType::class)],
            'company_id'                => ['nullable', 'integer', 'exists:companies,id'],
            'currency_id'               => ['nullable', 'integer', 'exists:currencies,id'],
            'default_account_id'        => ['nullable', 'integer', 'exists:accounts_accounts,id'],
            'suspense_account_id'       => ['nullable', 'integer', 'exists:accounts_accounts,id'],
            'profit_account_id'         => ['nullable', 'integer', 'exists:accounts_accounts,id'],
            'loss_account_id'           => ['nullable', 'integer', 'exists:accounts_accounts,id'],
            'bank_account_id'           => ['nullable', 'integer', 'exists:partner_bank_accounts,id'],
            'color'                     => ['nullable', 'string', 'max:7'],
            'show_on_dashboard'         => ['nullable', 'boolean'],
            'refund_order'              => ['nullable', 'boolean'],
            'payment_order'             => ['nullable', 'boolean'],
        ];
    }

    /**
     * Get body parameters for Scribe documentation.
     */
    public function bodyParameters(): array
    {
        return [
            'name' => [
                'description' => 'Journal name',
                'example'     => 'Bank Journal',
            ],
            'code' => [
                'description' => 'Journal code (max 5 chars)',
                'example'     => 'BNK',
            ],
            'type' => [
                'description' => 'Journal type',
                'example'     => JournalType::class,
            ],
            'company_id' => [
                'description' => 'Company ID',
                'example'     => 1,
            ],
            'currency_id' => [
                'description' => 'Currency ID',
                'example'     => 1,
            ],
            'default_account_id' => [
                'description' => 'Default account ID',
                'example'     => 1,
            ],
            'suspense_account_id' => [
                'description' => 'Suspense account ID',
                'example'     => 2,
            ],
            'profit_account_id' => [
                'description' => 'Profit account ID',
                'example'     => 3,
            ],
            'loss_account_id' => [
                'description' => 'Loss account ID',
                'example'     => 4,
            ],
            'bank_account_id' => [
                'description' => 'Bank account ID',
                'example'     => 1,
            ],
            'color' => [
                'description' => 'Journal color (hex)',
                'example'     => '#3b82f6',
            ],
            'show_on_dashboard' => [
                'description' => 'Show on dashboard',
                'example'     => true,
            ],
            'refund_order' => [
                'description' => 'Refund order flag',
                'example'     => false,
            ],
            'payment_order' => [
                'description' => 'Payment order flag',
                'example'     => false,
            ],
        ];
    }
}
