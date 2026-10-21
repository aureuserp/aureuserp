<?php

namespace Webkul\PointOfSale\Http\Requests;

class DraftSyncRequest extends OrderSyncRequest
{
    public function rules(): array
    {
        $rules = parent::rules();

        $rules['orders.*.lines'] = ['present', 'array'];

        unset(
            $rules['orders.*.payments'],
            $rules['orders.*.payments.*.uuid'],
            $rules['orders.*.payments.*.payment_method_id'],
            $rules['orders.*.payments.*.amount'],
        );

        return $rules;
    }
}
