<?php

return [
    'title' => 'شروط التسليم',

    'navigation' => [
        'title' => 'شروط التسليم',
        'group' => 'الفوترة',
    ],

    'global-search' => [
        'name' => 'الاسم',
        'code' => 'الرمز',
    ],

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
                    'title' => 'تم تحديث شرط التسليم',
                    'body'  => 'تم تحديث شرط التسليم بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف شرط التسليم',
                    'body'  => 'تم حذف شرط التسليم بنجاح.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'تم استعادة شرط التسليم',
                    'body'  => 'تم استعادة شرط التسليم بنجاح.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'تم استعادة شروط التسليم',
                    'body'  => 'تم استعادة شروط التسليم بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف شروط التسليم',
                    'body'  => 'تم حذف شروط التسليم بنجاح.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'تم حذف شروط التسليم نهائياً',
                    'body'  => 'تم حذف شروط التسليم نهائياً بنجاح.',
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
