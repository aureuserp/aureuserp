<?php

return [
    'label' => 'Labels',

    'form' => [
        'fields' => [
            'type'          => 'Type Of Labels',
            'quantity'      => 'Quantity',
            'format'        => 'Format',
            'layout'        => 'Layout Of Labels',
            'quantity-type' => 'Quantity To Print',
            'quantity'      => 'Quantity',

            'quantity-type-options' => [
                'operation' => 'Operation Quantity',
                'custom'    => 'Custom Quantity',
                'per-slot'  => 'One per lot/SN',
                'per-unit'  => 'One per unit',
            ],

            'type-options' => [
                'product' => 'Product Labels',
                'lot'     => 'Lot/SN Labels',
            ],

            'format-options' => [
                'dymo'       => 'Dymo',
                '2x7_price'  => '2x7 with price',
                '4x7_price'  => '4x7 with price',
                '4x12'       => '4x12',
                '4x12_price' => '4x12 with price',
            ],
        ],
    ],
];
