<?php

return [
    'navigation' => [
        'title' => 'خطط الأنشطة',
    ],

    'form' => [
        'name'   => 'الاسم',
        'status' => 'الحالة',
    ],

    'table' => [
        'columns' => [
            'name'       => 'الاسم',
            'status'     => 'الحالة',
            'created-at' => 'تاريخ الإنشاء',
            'updated-at' => 'تاريخ التحديث',
        ],

        'groups' => [
            'name'       => 'الاسم',
            'status'     => 'الحالة',
            'created-at' => 'تاريخ الإنشاء',
            'updated-at' => 'تاريخ التحديث',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'تم استعادة خطة النشاط',
                    'body'  => 'تم استعادة خطة النشاط بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف خطة النشاط',
                    'body'  => 'تم حذف خطة النشاط بنجاح.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'تم حذف خطة النشاط نهائياً',
                    'body'  => 'تم حذف خطة النشاط نهائياً بنجاح.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'تم استعادة خطط الأنشطة',
                    'body'  => 'تم استعادة خطط الأنشطة بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف خطط الأنشطة',
                    'body'  => 'تم حذف خطط الأنشطة بنجاح.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'تم حذف خطط الأنشطة نهائياً',
                    'body'  => 'تم حذف خطط الأنشطة نهائياً بنجاح.',
                ],
            ],
        ],

        'empty-state' => [
            'create' => [
                'notification' => [
                    'title' => 'تم إنشاء خطة النشاط',
                    'body'  => 'تم إنشاء خطة النشاط بنجاح.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'name'   => 'الاسم',
        'status' => 'الحالة',
    ],
];
