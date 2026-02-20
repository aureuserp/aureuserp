<?php

return [
    'form' => [
        'name'       => 'الاسم',
        'short-name' => 'الاسم المختصر',
    ],

    'table' => [
        'columns' => [
            'name'       => 'الاسم',
            'short-name' => 'الاسم المختصر',
            'created-at' => 'تاريخ الإنشاء',
            'updated-at' => 'تاريخ التحديث',
        ],

        'filters' => [
            'creator' => 'المُنشئ',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'تم تحديث اللقب',
                    'body'  => 'تم تحديث اللقب بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف اللقب',
                    'body'  => 'تم حذف اللقب بنجاح.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'تم حذف الألقاب',
                    'body'  => 'تم حذف الألقاب بنجاح.',
                ],
            ],
        ],
    ],
];
