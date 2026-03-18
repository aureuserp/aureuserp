<?php

namespace Webkul\Inventory\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductQuantityCountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'counted_quantity' => ['required', 'numeric', 'min:0', 'max:99999999999'],
        ];
    }

    public function bodyParameters(): array
    {
        return [
            'counted_quantity' => [
                'description' => 'Counted quantity entered during inventory adjustment.',
                'example'     => 25,
            ],
        ];
    }
}
