<?php

return [
    'form' => [
        'name'      => 'الاسم',
        'full-name' => 'الاسم الكامل',
    ],

    'table' => [
        'columns' => [
            'name'       => 'الاسم',
            'full-name'  => 'الاسم الكامل',
            'created-at' => 'تاريخ الإنشاء',
            'updated-at' => 'تاريخ التحديث',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'تم تحديث القطاع',
                    'body'  => 'تم تحديث القطاع بنجاح.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'تم استعادة القطاع',
                    'body'  => 'تم استعادة القطاع بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف القطاع',
                    'body'  => 'تم حذف القطاع بنجاح.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'تم حذف القطاع نهائياً',
                    'body'  => 'تم حذف القطاع نهائياً بنجاح.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'تم استعادة القطاعات',
                    'body'  => 'تم استعادة القطاعات بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف القطاعات',
                    'body'  => 'تم حذف القطاعات بنجاح.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'تم حذف القطاعات نهائياً',
                    'body'  => 'تم حذف القطاعات نهائياً بنجاح.',
                ],
            ],
        ],
    ],
];
