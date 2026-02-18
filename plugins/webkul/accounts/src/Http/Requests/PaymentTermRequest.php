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
            'name'       => ['required', 'string', 'max:255'],
            'company_id' => ['nullable', 'integer', 'exists:companies,id'],
            'note'       => ['nullable', 'string'],
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
            'note' => [
                'description' => 'Payment term notes',
                'example'     => 'Payment due within 30 days',
            ],
            'company_id' => [
                'description' => 'Company ID',
                'example'     => 1,
            ],
        ];
    }
}
