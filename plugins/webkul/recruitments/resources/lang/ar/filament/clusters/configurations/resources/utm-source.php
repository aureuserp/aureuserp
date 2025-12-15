<?php

return [
    'title' => 'المصادر',

    'navigation' => [
        'title' => 'المصادر',
        'group' => 'روابط UTM',
    ],

    'groups' => [
        'status'     => 'الحالة',
        'created-by' => 'أنشئ بواسطة',
        'created-at' => 'تاريخ الإنشاء',
        'updated-at' => 'تاريخ التحديث',
    ],

    'form' => [
        'fields' => [
            'name'             => 'الاسم',
            'name-placeholder' => 'أدخل اسم المصدر',
            'status'           => 'الحالة',
        ],
    ],

    'table' => [
        'columns' => [
            'id'         => 'المعرف',
            'name'       => 'الاسم',
            'status'     => 'الحالة',
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
                    'title' => 'تم تحديث المصدر',
                    'body'  => 'تم تحديث المصدر بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف المصدر',
                    'body'  => 'تم حذف المصدر بنجاح.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'تم حذف المصادر',
                    'body'  => 'تم حذف المصادر بنجاح.',
                ],
            ],
        ],

        'empty-state-action' => [
            'create' => [
                'notification' => [
                    'title' => 'تم إنشاء المصدر',
                    'body'  => 'تم إنشاء المصدر بنجاح.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'name' => 'الاسم',
    ],
];
