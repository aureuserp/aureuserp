<?php

return [
    'form' => [
        'sections' => [
            'fields' => [
                'company'            => 'الشركة',
                'country'            => 'الدولة',
                'name'               => 'الاسم',
                'preceding-subtotal' => 'المجموع الفرعي السابق',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'company'            => 'الشركة',
            'country'            => 'الدولة',
            'created-by'         => 'أنشئ بواسطة',
            'name'               => 'الاسم',
            'preceding-subtotal' => 'المجموع الفرعي السابق',
            'created-at'         => 'تاريخ الإنشاء',
            'updated-at'         => 'تاريخ التحديث',
        ],

        'groups' => [
            'name'       => 'الاسم',
            'company'    => 'الشركة',
            'country'    => 'الدولة',
            'created-by' => 'أنشئ بواسطة',
            'created-at' => 'تاريخ الإنشاء',
            'updated-at' => 'تاريخ التحديث',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'تم حذف مجموعة الضرائب',
                        'body'  => 'تم حذف مجموعة الضرائب بنجاح.',
                    ],

                    'error' => [
                        'title' => 'تعذر حذف مجموعة الضرائب',
                        'body'  => 'لا يمكن حذف مجموعة الضرائب لأنها قيد الاستخدام حالياً.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'تم حذف مجموعات الضرائب',
                        'body'  => 'تم حذف مجموعات الضرائب بنجاح.',
                    ],

                    'error' => [
                        'title' => 'تعذر حذف مجموعات الضرائب',
                        'body'  => 'لا يمكن حذف مجموعات الضرائب لأنها قيد الاستخدام حالياً.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'entries' => [
                'company'            => 'الشركة',
                'country'            => 'الدولة',
                'name'               => 'الاسم',
                'preceding-subtotal' => 'المجموع الفرعي السابق',
            ],
        ],
    ],
];
