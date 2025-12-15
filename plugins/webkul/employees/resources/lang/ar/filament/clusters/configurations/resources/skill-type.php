<?php

return [
    'title' => 'أنواع المهارات',

    'navigation' => [
        'title' => 'أنواع المهارات',
        'group' => 'الموظفين',
    ],

    'form' => [
        'sections' => [
            'fields' => [
                'name'       => 'الاسم',
                'color'      => 'اللون',
                'status'     => 'الحالة',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'id'         => 'المعرف',
            'name'       => 'نوع المهارة',
            'status'     => 'الحالة',
            'color'      => 'اللون',
            'skills'     => 'المهارات',
            'levels'     => 'المستويات',
            'created-by' => 'أنشئ بواسطة',
            'created-at' => 'تاريخ الإنشاء',
            'updated-at' => 'تاريخ التحديث',
        ],

        'filters' => [
            'skill-levels' => 'مستويات المهارات',
            'skills'       => 'المهارات',
            'created-by'   => 'أنشئ بواسطة',
            'status'       => 'الحالة',
            'updated-at'   => 'تاريخ التحديث',
            'created-at'   => 'تاريخ الإنشاء',
        ],

        'groups' => [
            'name'       => 'نوع المهارة',
            'color'      => 'اللون',
            'status'     => 'الحالة',
            'created-by' => 'أنشئ بواسطة',
            'created-at' => 'تاريخ الإنشاء',
            'updated-at' => 'تاريخ التحديث',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'تم استعادة نوع المهارة',
                    'body'  => 'تم استعادة نوع المهارة بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف نوع المهارة',
                    'body'  => 'تم حذف نوع المهارة بنجاح.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'تم استعادة أنواع المهارات',
                    'body'  => 'تم استعادة أنواع المهارات بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف أنواع المهارات',
                    'body'  => 'تم حذف أنواع المهارات بنجاح.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'تم حذف أنواع المهارات نهائياً',
                    'body'  => 'تم حذف أنواع المهارات نهائياً بنجاح.',
                ],
            ],
        ],

        'empty-state-actions' => [
            'create' => [
                'notification' => [
                    'title' => 'أنواع المهارات',
                    'body'  => 'تم إنشاء نوع المهارة بنجاح.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'entries' => [
                'name'   => 'نوع المهارة',
                'color'  => 'اللون',
                'status' => 'الحالة',
            ],
        ],
    ],
];
