<?php

namespace Webkul\PointOfSale\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderRefundRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'quantities'   => ['required', 'array', 'min:1'],
            'quantities.*' => ['numeric', 'min:0'],
        ];
    }
}
