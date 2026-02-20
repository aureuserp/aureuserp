<?php

return [
    'form' => [
        'name'          => 'الاسم',
        'level'         => 'المستوى',
        'default-level' => 'المستوى الافتراضي',
    ],

    'table' => [
        'columns' => [
            'name'          => 'الاسم',
            'level'         => 'المستوى',
            'default-level' => 'المستوى الافتراضي',
            'created-at'    => 'تاريخ الإنشاء',
            'updated-at'    => 'تاريخ التحديث',
        ],

        'groups' => [
            'created-at' => 'تاريخ الإنشاء',
        ],

        'filters' => [
            'deleted-records' => 'السجلات المحذوفة',
        ],

        'actions' => [
            'create' => [
                'notification' => [
                    'title' => 'تم إنشاء مستوى المهارة',
                    'body'  => 'تم إنشاء مستوى المهارة بنجاح.',
                ],
            ],

            'edit' => [
                'notification' => [
                    'title' => 'تم تحديث مستوى المهارة',
                    'body'  => 'تم تحديث مستوى المهارة بنجاح.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'تم استعادة مستوى المهارة',
                    'body'  => 'تم استعادة مستوى المهارة بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف مستوى المهارة',
                    'body'  => 'تم حذف مستوى المهارة بنجاح.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'تم حذف مستويات المهارات',
                    'body'  => 'تم حذف مستويات المهارات بنجاح.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'تم حذف مستويات المهارات نهائياً',
                    'body'  => 'تم حذف مستويات المهارات نهائياً بنجاح.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'تم استعادة مستويات المهارات',
                    'body'  => 'تم استعادة مستويات المهارات بنجاح.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'name'          => 'الاسم',
            'level'         => 'المستوى',
            'default-level' => 'المستوى الافتراضي',
        ],
    ],
];
