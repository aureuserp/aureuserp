<?php

return [
    'header-actions' => [
        'print' => [
            'label' => 'طباعة الملصقات',

            'form' => [
                'fields' => [
                    'quantity' => 'عدد الملصقات',
                    'format'   => 'التنسيق',

                    'format-options' => [
                        'dymo'       => 'Dymo',
                        '2x7_price'  => '2x7 مع السعر',
                        '4x7_price'  => '4x7 مع السعر',
                        '4x12'       => '4x12',
                        '4x12_price' => '4x12 مع السعر',
                    ],
                ],
            ],
        ],

        'delete' => [
            'notification' => [
                'title' => 'تم حذف المنتج',
                'body'  => 'تم حذف المنتج بنجاح.',
            ],
        ],
    ],
];
