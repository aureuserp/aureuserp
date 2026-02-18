<?php

namespace Webkul\Account\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IncotermRequest extends FormRequest
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
            'code' => ['required', 'string', 'max:3'],
            'name' => ['required', 'string', 'max:255'],
        ];
    }

    /**
     * Get body parameters for Scribe documentation.
     */
    public function bodyParameters(): array
    {
        return [
            'code' => [
                'description' => 'Incoterm code (3 characters)',
                'example'     => 'EXW',
            ],
            'name' => [
                'description' => 'Incoterm name',
                'example'     => 'Ex Works',
            ],
        ];
    }
}
