<?php

return [
    'label' => 'الملصقات',

    'form' => [
        'fields' => [
            'type'          => 'نوع الملصقات',
            'quantity'      => 'الكمية',
            'format'        => 'التنسيق',
            'layout'        => 'تخطيط الملصقات',
            'quantity-type' => 'الكمية للطباعة',
            'quantity'      => 'الكمية',

            'quantity-type-options' => [
                'operation' => 'كمية العملية',
                'custom'    => 'كمية مخصصة',
                'per-slot'  => 'واحد لكل دفعة/رقم تسلسلي',
                'per-unit'  => 'واحد لكل وحدة',
            ],

            'type-options' => [
                'product' => 'ملصقات المنتج',
                'lot'     => 'ملصقات الدفعة/الرقم التسلسلي',
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
