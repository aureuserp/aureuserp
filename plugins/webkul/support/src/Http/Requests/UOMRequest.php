<?php

namespace Webkul\Support\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Webkul\Support\Enums\UOMType;

class UOMRequest extends FormRequest
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
            'type'        => ['required', Rule::enum(UOMType::class)],
            'name'        => ['required', 'string', 'max:255'],
            'factor'      => ['required', 'numeric', 'min:0'],
            'rounding'    => ['required', 'numeric', 'min:0'],
            'category_id' => ['required', 'integer', 'exists:unit_of_measure_categories,id'],
        ];
    }

    /**
     * Get body parameters for Scribe documentation.
     */
    public function bodyParameters(): array
    {
        return [
            'type'        => [
                'description' => 'UOM type (reference, bigger, smaller)',
                'example' => 'reference'
            ],
            'name'        => [
                'description' => 'UOM name',
                'example' => 'Kilogram'
            ],
            'factor'      => [
                'description' => 'Conversion factor',
                'example' => 1.0
            ],
            'rounding'    => [
                'description' => 'Rounding precision',
                'example' => 0.01
            ],
            'category_id' => [
                'description' => 'UOM category ID',
                'example' => 1
            ],
        ];
    }
}
