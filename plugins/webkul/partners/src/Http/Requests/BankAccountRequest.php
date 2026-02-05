<?php

namespace Webkul\Partner\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BankAccountRequest extends FormRequest
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
        $rules = [
            'account_number' => 'required|string|max:255',
            'can_send_money' => 'required|boolean',
            'bank_id'        => 'required|integer|exists:banks,id',
        ];

        // On update, make all fields optional
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules = array_map(function ($rule) {
                if (is_string($rule) && str_starts_with($rule, 'required')) {
                    return str_replace('required', 'sometimes|required', $rule);
                }

                return $rule;
            }, $rules);
        }

        return $rules;
    }

    /**
     * Get body parameters for API documentation.
     *
     * @return array<string, array<string, mixed>>
     */
    public function bodyParameters(): array
    {
        return [
            'account_number' => [
                'description' => 'Bank account number (max 255 characters).',
                'example'     => '1234567890',
            ],
            'can_send_money' => [
                'description' => 'Whether money can be sent from this account.',
                'example'     => true,
            ],
            'bank_id' => [
                'description' => 'Bank ID.',
                'example'     => 1,
            ],
        ];
    }
}
