<?php

namespace Webkul\Inventory\Http\Requests;

use Webkul\Account\Http\Requests\ProductRequest as BaseProductRequest;
use Webkul\Inventory\Enums\ProductTracking;

class ProductRequest extends BaseProductRequest
{
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'is_storable'         => 'nullable|boolean',
            'tracking'            => 'nullable|string|in:'.implode(',', array_column(ProductTracking::cases(), 'value')),
            'use_expiration_date' => 'nullable|boolean',
            'sale_delay'          => 'nullable|numeric|min:0|max:99999999999',
            'expiration_time'     => 'nullable|numeric|min:0|max:99999999999',
            'use_time'            => 'nullable|numeric|min:0|max:99999999999',
            'removal_time'        => 'nullable|numeric|min:0|max:99999999999',
            'alert_time'          => 'nullable|numeric|min:0|max:99999999999',
            'responsible_id'      => 'nullable|integer|exists:users,id',
            'routes'              => 'nullable|array',
            'routes.*'            => 'integer|exists:inventories_routes,id',
        ]);
    }

    public function bodyParameters(): array
    {
        return array_merge(parent::bodyParameters(), [
            'is_storable' => [
                'description' => 'Whether inventory is tracked for this product.',
                'example'     => true,
            ],
            'tracking' => [
                'description' => 'Inventory tracking method.',
                'example'     => 'qty',
            ],
            'use_expiration_date' => [
                'description' => 'Enable expiration dates for this product.',
                'example'     => false,
            ],
            'sale_delay' => [
                'description' => 'Delivery lead time in days.',
                'example'     => 2,
            ],
            'expiration_time' => [
                'description' => 'Expiration time in days.',
                'example'     => 0,
            ],
            'use_time' => [
                'description' => 'Best before time in days.',
                'example'     => 0,
            ],
            'removal_time' => [
                'description' => 'Removal time in days.',
                'example'     => 0,
            ],
            'alert_time' => [
                'description' => 'Alert time in days.',
                'example'     => 0,
            ],
            'responsible_id' => [
                'description' => 'Responsible user ID.',
                'example'     => 1,
            ],
            'routes' => [
                'description' => 'Inventory route IDs applicable to the product.',
                'example'     => [1, 2],
            ],
        ]);
    }
}
