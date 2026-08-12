<?php

return [
    'title' => 'المتغيرات',

    'form' => [
        'date'                   => 'التاريخ',
        'employee'               => 'الموظف',
        'description'            => 'الوصف',
        'time-spent'             => 'الوقت المستغرق',
        'time-spent-helper-text' => 'الوقت المستغرق بالساعات (مثال: 1.5 ساعة تعني ساعة و 30 دقيقة)',
    ],

    'table' => [
        'columns' => [
            'date'                   => 'التاريخ',
            'employee'               => 'الموظف',
            'description'            => 'الوصف',
            'time-spent'             => 'الوقت المستغرق',
            'time-spent-on-subtasks' => 'الوقت المستغرق على المهام الفرعية',
            'total-time-spent'       => 'إجمالي الوقت المستغرق',
            'remaining-time'         => 'الوقت المتبقي',
            'variant-values'         => 'قيم المتغير',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'تم حذف المتغير',
                    'body'  => 'تم حذف المتغير بنجاح.',
                ],
            ],

            'view' => [
                'extra-footer-actions' => [
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
                ],
            ],
        ],
    ],
];
