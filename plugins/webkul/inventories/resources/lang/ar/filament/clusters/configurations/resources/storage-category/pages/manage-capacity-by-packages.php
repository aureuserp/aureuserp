<?php

return [
    'title' => 'السعة حسب الطرود',

    'form' => [
        'package-type' => 'نوع الطرد',
        'qty'          => 'الكمية',
    ],

    'table' => [
        'header-actions' => [
            'create' => [
                'label' => 'إضافة سعة نوع طرد',

                'notification' => [
                    'title' => 'تم إنشاء سعة نوع الطرد',
                    'body'  => 'تم إضافة سعة نوع الطرد بنجاح.',
                ],
            ],
        ],

        'columns' => [
            'package-type' => 'نوع الطرد',
            'qty'          => 'الكمية',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'تم تحديث سعة نوع الطرد',
                    'body'  => 'تم تحديث سعة نوع الطرد بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف سعة نوع الطرد',
                    'body'  => 'تم حذف سعة نوع الطرد بنجاح.',
                ],
            ],
        ],
    ],
];
