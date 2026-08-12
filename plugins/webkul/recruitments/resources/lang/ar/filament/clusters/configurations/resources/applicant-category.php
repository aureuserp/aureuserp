<?php

return [
    'title' => 'الوسوم',

    'navigation' => [
        'title' => 'الوسوم',
        'group' => 'الطلبات',
    ],

    'form' => [
        'fields' => [
            'name'             => 'الاسم',
            'name-placeholder' => 'أدخل اسم الوسم',
            'color'            => 'اللون',
        ],
    ],

    'table' => [
        'columns' => [
            'id'         => 'المعرف',
            'name'       => 'الاسم',
            'color'      => 'اللون',
            'created-by' => 'أنشئ بواسطة',
            'created-at' => 'تاريخ الإنشاء',
            'updated-at' => 'تاريخ التحديث',
        ],

        'filters' => [
            'name'       => 'الاسم',
            'employee'   => 'الموظف',
            'created-by' => 'أنشئ بواسطة',
            'updated-at' => 'تاريخ التحديث',
            'created-at' => 'تاريخ الإنشاء',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'تم تحديث الوسوم',
                    'body'  => 'تم تحديث الوسوم بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف الوسوم',
                    'body'  => 'تم حذف الوسوم بنجاح.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'تم حذف فئات المتقدمين',
                    'body'  => 'تم حذف فئات المتقدمين بنجاح.',
                ],
            ],
        ],

        'empty-state-action' => [
            'create' => [
                'notification' => [
                    'title' => 'تم إنشاء الوسوم',
                    'body'  => 'تم إنشاء الوسوم بنجاح.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'name'  => 'الاسم',
        'color' => 'اللون',
    ],
];
