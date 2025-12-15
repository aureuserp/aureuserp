<?php

return [
    'title' => 'الوسائط',

    'navigation' => [
        'title' => 'الوسائط',
        'group' => 'روابط UTM',
    ],

    'form' => [
        'fields' => [
            'name'             => 'الاسم',
            'name-placeholder' => 'أدخل اسم الوسيط',
        ],
    ],

    'table' => [
        'columns' => [
            'id'         => 'المعرف',
            'name'       => 'الاسم',
            'created-by' => 'أنشئ بواسطة',
            'created-at' => 'تاريخ الإنشاء',
            'updated-at' => 'تاريخ التحديث',
        ],

        'filters' => [
            'name'       => 'الاسم',
            'created-by' => 'أنشئ بواسطة',
            'updated-at' => 'تاريخ التحديث',
            'created-at' => 'تاريخ الإنشاء',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'تم تحديث الوسيط',
                    'body'  => 'تم تحديث الوسيط بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف الوسيط',
                    'body'  => 'تم حذف الوسيط بنجاح.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'تم حذف الوسائط',
                    'body'  => 'تم حذف الوسائط بنجاح.',
                ],
            ],
        ],

        'empty-state-action' => [
            'create' => [
                'notification' => [
                    'title' => 'تم إنشاء الوسيط',
                    'body'  => 'تم إنشاء الوسيط بنجاح.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'name' => 'الاسم',
    ],
];
