<?php

return [
    'label' => 'طباعة الملصقات',

    'form' => [
        'fields' => [
            'quantity'      => 'الكمية',
            'format'        => 'التنسيق',
            'quantity-type' => 'الكمية المراد طباعتها',

            'quantity-type-options' => [
                'operation' => 'كمية العملية',
                'custom'    => 'كمية مخصصة',
            ],

            'format-options' => [
                'dymo'       => 'Dymo',
                '2x7_price'  => '2x7 مع السعر',
                '4x7_price'  => '4x7 مع السعر',
                '4x12'       => '4x12',
                '4x12_price' => '4x12 مع السعر',
            ],
        ],
    ],
];
