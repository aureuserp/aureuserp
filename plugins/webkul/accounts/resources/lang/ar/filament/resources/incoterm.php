<?php

return [
    'form' => [
        'fields' => [
            'code' => 'الرمز',
            'name' => 'الاسم',
        ],
    ],

    'table' => [
        'columns' => [
            'code'       => 'الرمز',
            'name'       => 'الاسم',
            'created-by' => 'أنشئ بواسطة',
        ],

        'groups' => [
            'code' => 'الرمز',
            'name' => 'الاسم',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'تم تحديث شرط التجارة',
                    'body'  => 'تم تحديث شرط التجارة بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف شرط التجارة',
                    'body'  => 'تم حذف شرط التجارة بنجاح.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'تم استعادة شرط التجارة',
                    'body'  => 'تم استعادة شرط التجارة بنجاح.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'تم استعادة شروط التجارة',
                    'body'  => 'تم استعادة شروط التجارة بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف شروط التجارة',
                    'body'  => 'تم حذف شروط التجارة بنجاح.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'تم حذف شروط التجارة نهائياً',
                    'body'  => 'تم حذف شروط التجارة نهائياً بنجاح.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'name' => 'الاسم',
            'code' => 'الرمز',
        ],
    ],
];
