<?php

return [
    'navigation' => [
        'title' => 'خطط النشاط',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'معلومات عامة',
                'fields' => [
                    'name'       => 'الاسم',
                    'status'     => 'الحالة',
                    'department' => 'القسم',
                    'company'    => 'الشركة',
                ],
            ],
        ],
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
            'activity-types' => 'أنواع النشاط',
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
                    'title' => 'تم استعادة خطط النشاط',
                    'body'  => 'تم استعادة خطط النشاط بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف خطط النشاط',
                    'body'  => 'تم حذف خطط النشاط بنجاح.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'تم حذف خطط النشاط نهائياً',
                    'body'  => 'تم حذف خطط النشاط نهائياً بنجاح.',
                ],
            ],
        ],

        'activity-plan' => [
            'create' => [
                'notification' => [
                    'title' => 'تم إنشاء خطة النشاط',
                    'body'  => 'تم إنشاء خطة النشاط بنجاح.',
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
        'sections' => [
            'general' => [
                'title'   => 'معلومات عامة',
                'entries' => [
                    'name'       => 'الاسم',
                    'status'     => 'الحالة',
                    'department' => 'القسم',
                    'manager'    => 'المدير',
                    'company'    => 'الشركة',
                ],
            ],
        ],
    ],
];
