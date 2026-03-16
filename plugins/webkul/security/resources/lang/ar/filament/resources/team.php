<?php

return [
    'title' => 'الفرق',

    'navigation' => [
        'title' => 'الفرق',
        'group' => 'الإعدادات',
    ],

    'form' => [
        'fields' => [
            'name' => 'الاسم',
        ],
    ],

    'table' => [
        'columns' => [
            'name'         => 'الاسم',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'تم تحديث الفريق',
                    'body'  => 'تم تحديث الفريق بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف الفريق',
                    'body'  => 'تم حذف الفريق بنجاح.',
                ],
            ],
        ],

        'empty-state-actions' => [
            'create' => [
                'notification' => [
                    'title' => 'تم إنشاء الفرق',
                    'body'  => 'تم إنشاء الفرق بنجاح.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'entries' => [
                'name'          => 'الاسم',
                'job-title'     => 'المسمى الوظيفي',
                'work-email'    => 'البريد الإلكتروني للعمل',
                'work-mobile'   => 'جوال العمل',
                'work-phone'    => 'هاتف العمل',
                'manager'       => 'المدير',
                'department'    => 'القسم',
                'job-position'  => 'المنصب الوظيفي',
                'team-tags'     => 'وسوم الفريق',
                'coach'         => 'المدرب',
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'name' => 'الاسم',
        ],
    ],
];
