<?php

namespace Webkul\Account\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH');
        $requiredRule = $isUpdate ? ['sometimes', 'required'] : ['required'];

        return [
            'name'       => [...$requiredRule, 'string', 'max:255'],
            'company_id' => ['nullable', 'integer', 'exists:companies,id'],
            'note'       => ['nullable', 'string'],
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * Sanitize the note field to strip unsafe HTML/JavaScript while
     * preserving legitimate formatting.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('note')) {
            $this->merge([
                'note' => $this->note === null
                    ? null
                    : (string) str($this->note)->sanitizeHtml(),
            ]);
        }
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
