<?php

return [
    'title' => 'الأقسام',

    'navigation' => [
        'title' => 'الأقسام',
        'group' => 'الموظفون',
    ],

    'global-search' => [
        'name'               => 'القسم',
        'department-manager' => 'المدير',
        'company'            => 'الشركة',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'معلومات عامة',

                'fields' => [
                    'name'                => 'الاسم',
                    'manager'             => 'المدير',
                    'parent-department'   => 'القسم الأصلي',
                    'manager-placeholder' => 'اختر المدير',
                    'company'             => 'الشركة',
                    'company-placeholder' => 'اختر الشركة',
                    'color'               => 'اللون',
                ],
            ],

            'additional' => [
                'title'       => 'معلومات إضافية',
                'description' => 'معلومات إضافية حول هذا القسم.',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'         => 'الاسم',
            'manager-name' => 'المدير',
            'company-name' => 'الشركة',
        ],

        'groups' => [
            'name'       => 'الاسم',
            'manager'    => 'المدير',
            'company'    => 'الشركة',
            'updated-at' => 'تاريخ التحديث',
            'created-at' => 'تاريخ الإنشاء',
        ],

        'filters' => [
            'name'         => 'الاسم',
            'manager-name' => 'المدير',
            'company-name' => 'الشركة',
            'updated-at'   => 'تاريخ التحديث',
            'created-at'   => 'تاريخ الإنشاء',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'تم استعادة القسم',
                    'body'  => 'تم استعادة القسم بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف القسم',
                    'body'  => 'تم حذف القسم بنجاح.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'تم حذف القسم نهائياً',
                    'body'  => 'تم حذف القسم نهائياً بنجاح.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'تم استعادة الأقسام',
                    'body'  => 'تم استعادة الأقسام بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف الأقسام',
                    'body'  => 'تم حذف الأقسام بنجاح.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'تم حذف الأقسام نهائياً',
                    'body'  => 'تم حذف الأقسام نهائياً بنجاح.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'عام',

                'entries' => [
                    'name'            => 'الاسم',
                    'manager'         => 'المدير',
                    'company'         => 'الشركة',
                    'color'           => 'اللون',
                    'hierarchy-title' => 'الهيكل التنظيمي للقسم',
                ],
            ],
        ],
    ],
];
