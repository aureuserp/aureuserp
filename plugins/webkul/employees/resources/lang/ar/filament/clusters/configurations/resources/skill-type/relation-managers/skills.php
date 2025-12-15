<?php

return [
    'form' => [
        'name' => 'الاسم',
    ],

    'table' => [
        'columns' => [
            'name'       => 'الاسم',
            'created-at' => 'تاريخ الإنشاء',
            'updated-at' => 'تاريخ التحديث',
        ],

        'groups' => [
            'created-at' => 'تاريخ الإنشاء',
        ],

        'filters' => [
            'deleted-records' => 'السجلات المحذوفة',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'تم تحديث المهارة',
                    'body'  => 'تم تحديث المهارة بنجاح.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'تم استعادة المهارة',
                    'body'  => 'تم استعادة المهارة بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف المهارة',
                    'body'  => 'تم حذف المهارة بنجاح.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'تم حذف المهارات',
                    'body'  => 'تم حذف المهارات بنجاح.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'تم حذف المهارات نهائياً',
                    'body'  => 'تم حذف المهارات نهائياً بنجاح.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'تم استعادة المهارات',
                    'body'  => 'تم استعادة المهارات بنجاح.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'name' => 'الاسم',
        ],
    ],
];
