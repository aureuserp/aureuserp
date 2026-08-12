<?php

return [
    'navigation' => [
        'title' => 'المراحل',
    ],

    'form' => [
        'fields' => [
            'name' => 'الاسم',
            'done' => 'منتهية',
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'الاسم',
            'done'       => 'منتهية',
            'created-at' => 'تاريخ الإنشاء',
        ],

        'groups' => [
            'done'       => 'منتهية',
            'created-at' => 'تاريخ الإنشاء',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'تم تحديث المرحلة',
                    'body'  => 'تم تحديث المرحلة بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف المرحلة',
                    'body'  => 'تم حذف المرحلة بنجاح.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'تم حذف المراحل',
                    'body'  => 'تم حذف المراحل بنجاح.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'المعلومات العامة',

                'entries' => [
                    'name' => 'الاسم',
                    'done' => 'منتهية',
                ],
            ],
        ],
    ],
];
