<?php

return [
    'title' => 'الوسوم',

    'navigation' => [
        'title' => 'الوسوم',
        'group' => 'الموظف',
    ],

    'groups' => [
        'status'     => 'الحالة',
        'created-by' => 'أنشئ بواسطة',
        'created-at' => 'تاريخ الإنشاء',
        'updated-at' => 'تاريخ التحديث',
    ],

    'form' => [
        'fields' => [
            'name'  => 'الاسم',
            'color' => 'اللون',
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
            'created-by' => 'أنشئ بواسطة',
            'updated-by' => 'حُدّث بواسطة',
            'updated-at' => 'تاريخ التحديث',
            'created-at' => 'تاريخ الإنشاء',
        ],

        'groups' => [
            'name'         => 'الاسم',
            'job-position' => 'المسمى الوظيفي',
            'color'        => 'اللون',
            'created-by'   => 'أنشئ بواسطة',
            'created-at'   => 'تاريخ الإنشاء',
            'updated-at'   => 'تاريخ التحديث',
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

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'تم حذف الوسوم',
                    'body'  => 'تم حذف الوسوم بنجاح.',
                ],
            ],
        ],

        'empty-state-action' => [
            'create' => [
                'notification' => [
                    'title' => 'تم إنشاء الوسم',
                    'body'  => 'تم إنشاء الوسم بنجاح.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'name'  => 'الاسم',
        'color' => 'اللون',
    ],
];
