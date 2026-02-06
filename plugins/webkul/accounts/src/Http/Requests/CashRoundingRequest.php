<?php

namespace Webkul\Account\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Webkul\Account\Enums\RoundingMethod;
use Webkul\Account\Enums\RoundingStrategy;

class CashRoundingRequest extends FormRequest
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
            'name'              => ['required', 'string', 'max:255'],
            'strategy'          => ['required', Rule::enum(RoundingStrategy::class)],
            'rounding_method'   => ['required', Rule::enum(RoundingMethod::class)],
            'rounding'          => ['required', 'numeric', 'min:0'],
            'profit_account_id' => ['nullable', 'integer', 'exists:accounts_accounts,id'],
            'loss_account_id'   => ['nullable', 'integer', 'exists:accounts_accounts,id'],
        ];
    }

    /**
     * Get body parameters for Scribe documentation.
     */
    public function bodyParameters(): array
    {
        return [
            'name' => [
                'description' => 'Cash rounding name',
                'example'     => '0.05 Rounding',
            ],
            'strategy' => [
                'description' => 'Rounding strategy',
                'example'     => RoundingStrategy::BIGGEST_TAX->value,
            ],
            'rounding_method' => [
                'description' => 'Rounding method',
                'example'     => RoundingMethod::UP->value,
            ],
            'rounding' => [
                'description' => 'Rounding precision',
                'example'     => 0.05,
            ],
            'profit_account_id' => [
                'description' => 'Profit account ID',
                'example'     => 1,
            ],
            'loss_account_id' => [
                'description' => 'Loss account ID',
                'example'     => 2,
            ],
        ];
    }
}
