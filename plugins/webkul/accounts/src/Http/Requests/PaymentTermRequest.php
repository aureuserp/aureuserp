<?php

namespace Webkul\Account\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentTermRequest extends FormRequest
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
            'name'                 => ['required', 'string', 'max:255'],
            'company_id'           => ['nullable', 'integer', 'exists:companies,id'],
            'note'                 => ['nullable', 'string'],
            'display_on_invoice'   => ['nullable', 'boolean'],
            'early_pay_discount'   => ['nullable', 'boolean'],
            'discount_percentage'  => ['nullable', 'numeric', 'min:0', 'max:100'],
            'discount_days'        => ['nullable', 'integer', 'min:0'],
        ];
    }

    /**
     * Get body parameters for Scribe documentation.
     */
    public function bodyParameters(): array
    {
        return [
            'name' => [
                'description' => 'Payment term name',
                'example'     => '30 Days',
            ],
            'company_id' => [
                'description' => 'Company ID',
                'example'     => 1,
            ],
            'note' => [
                'description' => 'Payment term notes',
                'example'     => 'Payment due within 30 days',
            ],
            'display_on_invoice' => [
                'description' => 'Display on invoice',
                'example'     => true,
            ],
            'early_pay_discount' => [
                'description' => 'Early payment discount enabled',
                'example'     => false,
            ],
            'discount_percentage' => [
                'description' => 'Discount percentage',
                'example'     => 2.5,
            ],
            'discount_days' => [
                'description' => 'Days for early discount',
                'example'     => 10,
            ],
        ];
    }
}
