<?php

namespace Webkul\Support\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StateRequest extends FormRequest
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
        $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH');

        $rules = [
            'name'       => ($isUpdate ? 'sometimes|' : '').'required|string|max:255',
            'code'       => 'nullable|string|max:50',
            'country_id' => ($isUpdate ? 'sometimes|' : '').'required|exists:countries,id',
        ];

        return $rules;
    }

    /**
     * Get body parameters for Scribe documentation.
     */
    public function bodyParameters(): array
    {
        return [
            'name'       => [
                'description' => 'State/Province name',
                'example' => 'California'
            ],
            'code'       => [
                'description' => 'State/Province code',
                'example' => 'CA'
            ],
            'country_id' => [
                'description' => 'Country ID',
                'example' => 233
            ],
        ];
    }
}
