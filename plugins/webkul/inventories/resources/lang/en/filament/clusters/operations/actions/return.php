<?php

return [
    'label' => 'Return',

    'modal' => [
        'form' => [
            'columns' => [
                'product'                 => 'Product',
                'quantity'                => 'Quantity',
                'uom'                     => 'UOM',
                'excess-quantity-tooltip' => 'The quantity to return is greater than the remaining returnable quantity.',
            ],
        ],
    ],

    'notification' => [
        'no-products' => [
            'body' => 'No products to return (only lines in Done state and not fully returned yet can be returned).',
        ],
        'no-quantities' => [
            'body' => 'Please specify at least one non-zero quantity.',
        ],
    ],
];
