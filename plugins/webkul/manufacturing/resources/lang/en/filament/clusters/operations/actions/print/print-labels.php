<?php

return [
    'label' => 'Print Labels',

    'form' => [
        'fields' => [
            'quantity'      => 'Quantity',
            'format'        => 'Format',
            'quantity-type' => 'Quantity To Print',

            'quantity-type-options' => [
                'operation' => 'Operation Quantity',
                'custom'    => 'Custom Quantity',
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
