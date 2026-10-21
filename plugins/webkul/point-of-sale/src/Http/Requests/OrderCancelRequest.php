<?php

namespace Webkul\PointOfSale\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderCancelRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'uuid' => ['required', 'uuid', 'exists:pos_orders,uuid'],
        ];
    }
}
