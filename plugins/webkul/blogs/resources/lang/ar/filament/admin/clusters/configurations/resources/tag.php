<?php

return [
    'navigation' => [
        'title' => 'الوسوم',
        'group' => 'المدونة',
    ],

    'form' => [
        'name'  => 'الاسم',
        'color' => 'اللون',
    ],

    'table' => [
        'columns' => [
            'name'       => 'الاسم',
            'color'      => 'اللون',
            'created-at' => 'تاريخ الإنشاء',
            'updated-at' => 'تاريخ التحديث',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'تم تحديث الوسم',
                    'body'  => 'تم تحديث الوسم بنجاح.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'تم استعادة الوسم',
                    'body'  => 'تم استعادة الوسم بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف الوسم',
                    'body'  => 'تم حذف الوسم بنجاح.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'تم حذف الوسم نهائياً',
                    'body'  => 'تم حذف الوسم نهائياً بنجاح.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'تم استعادة الوسوم',
                    'body'  => 'تم استعادة الوسوم بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف الوسوم',
                    'body'  => 'تم حذف الوسوم بنجاح.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'تم حذف الوسوم نهائياً',
                    'body'  => 'تم حذف الوسوم نهائياً بنجاح.',
                ],
            ],
        ],
    ],
];
