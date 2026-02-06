<?php

namespace Webkul\Account\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Webkul\Account\Enums\DelayType;
use Webkul\Account\Enums\DueTermValue;

class PaymentDueTermRequest extends FormRequest
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
            'value'           => ['required', Rule::enum(DueTermValue::class)],
            'value_amount'    => ['required', 'numeric', 'min:0', 'max:100'],
            'delay_type'      => ['required', Rule::enum(DelayType::class)],
            'nb_days'         => ['required', 'integer', 'min:0'],
            'days_next_month' => ['nullable', 'integer', 'min:1', 'max:31'],
        ];
    }

    /**
     * Get body parameters for Scribe documentation.
     */
    public function bodyParameters(): array
    {
        return [
            'value' => [
                'description' => 'Due term value type',
                'example'     => DueTermValue::PERCENT->value,
            ],
            'value_amount' => [
                'description' => 'Value amount (percentage or fixed amount)',
                'example'     => 100,
            ],
            'delay_type' => [
                'description' => 'Delay type',
                'example'     => DelayType::DAYS_AFTER->value,
            ],
            'nb_days' => [
                'description' => 'Number of days',
                'example'     => 30,
            ],
            'days_next_month' => [
                'description' => 'Day of next month (1-31)',
                'example'     => 15,
            ],
        ];
    }
}
