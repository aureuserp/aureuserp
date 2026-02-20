<?php

return [
    'navigation' => [
        'title' => 'خطط الأنشطة',
        'group' => 'الأنشطة',
    ],

    'global-search' => [
        'name'         => 'القسم',
        'department'   => 'القسم',
        'manager'      => 'المدير',
        'company'      => 'الشركة',
        'plugin'       => 'الإضافة',
        'creator-name' => 'أنشئ بواسطة',
    ],

    'table' => [
        'columns' => [
            'name'       => 'الاسم',
            'status'     => 'الحالة',
            'department' => 'القسم',
            'company'    => 'الشركة',
            'manager'    => 'المدير',
            'created-by' => 'أنشئ بواسطة',
            'created-at' => 'تاريخ الإنشاء',
            'updated-at' => 'تاريخ التحديث',
        ],

        'filters' => [
            'name'           => 'الاسم',
            'plugin'         => 'الإضافة',
            'activity-types' => 'أنواع الأنشطة',
            'company'        => 'الشركة',
            'department'     => 'القسم',
            'is-active'      => 'الحالة',
            'updated-at'     => 'تاريخ التحديث',
            'created-at'     => 'تاريخ الإنشاء',
        ],

        'groups' => [
            'status'     => 'الحالة',
            'name'       => 'الاسم',
            'created-by' => 'أنشئ بواسطة',
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
];
