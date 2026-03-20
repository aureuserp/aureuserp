<?php

return [
    'title' => 'الوسم',

    'navigation' => [
        'title' => 'الوسم',
        'group' => 'أوامر المبيعات',
    ],

    'form' => [
        'fields' => [
            'name'  => 'الاسم',
            'color' => 'اللون',
        ],
    ],

    'table' => [
        'columns' => [
            'created-by' => 'أنشئ بواسطة',
            'name'       => 'الاسم',
            'color'      => 'اللون',
        ],
        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'تم تحديث وسم المنتج',
                    'body'  => 'تم تحديث وسم المنتج بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف وسم المنتج',
                    'body'  => 'تم حذف وسم المنتج بنجاح.',
                ],
            ],
        ],
        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'تم حذف وسم المنتج',
                    'body'  => 'تم حذف وسم المنتج بنجاح.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'name'  => 'الاسم',
            'color' => 'اللون',
        ],
    ],
];
