<?php

return [
    'model-label'        => 'الوسم',
    'plural-model-label' => 'الوسوم',

    'navigation' => [
        'title' => 'الوسوم',
        'group' => 'مسار المبيعات',
    ],

    'form' => [
        'fields' => [
            'name'  => 'اسم الوسم',
            'color' => 'اللون',
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'اسم الوسم',
            'color'      => 'اللون',
            'created-by' => 'أنشئ بواسطة',
            'created-at' => 'تاريخ الإنشاء',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'تم تحديث الوسم',
                    'body'  => 'تم تحديث الوسم بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف الوسم',
                    'body'  => 'تم حذف الوسم بنجاح.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'name'  => 'اسم الوسم',
            'color' => 'اللون',
        ],
    ],
];
