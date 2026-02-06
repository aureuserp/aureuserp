<?php

namespace Webkul\Account\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Webkul\Account\Enums\AccountType;

class AccountRequest extends FormRequest
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
            'name'         => ['required', 'string', 'max:255'],
            'code'         => ['required', 'string', 'max:64'],
            'account_type' => ['required', Rule::enum(AccountType::class)],
            'currency_id'  => ['nullable', 'integer', 'exists:currencies,id'],
            'note'         => ['nullable', 'string'],
            'deprecated'   => ['nullable', 'boolean'],
            'reconcile'    => ['nullable', 'boolean'],
            'non_trade'    => ['nullable', 'boolean'],
        ];
    }

    /**
     * Get body parameters for Scribe documentation.
     */
    public function bodyParameters(): array
    {
        return [
            'name' => [
                'description' => 'Account name',
                'example'     => 'Cash and Bank',
            ],
            'code' => [
                'description' => 'Account code',
                'example'     => '1000',
            ],
            'account_type' => [
                'description' => 'Account type',
                'example'     => AccountType::ASSET_RECEIVABLE->value,
            ],
            'currency_id' => [
                'description' => 'Currency ID',
                'example'     => 1,
            ],
            'note' => [
                'description' => 'Account notes',
                'example'     => 'Primary cash account',
            ],
            'deprecated' => [
                'description' => 'Mark account as deprecated',
                'example'     => false,
            ],
            'reconcile' => [
                'description' => 'Enable reconciliation',
                'example'     => true,
            ],
            'non_trade' => [
                'description' => 'Non-trade account flag',
                'example'     => false,
            ],
        ];
    }
}
