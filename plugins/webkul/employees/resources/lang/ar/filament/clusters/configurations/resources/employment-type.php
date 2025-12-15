<?php

return [
    'title' => 'أنواع التوظيف',

    'navigation' => [
        'title' => 'أنواع التوظيف',
        'group' => 'التوظيف',
    ],

    'form' => [
        'fields' => [
            'name'    => 'نوع التوظيف',
            'code'    => 'الرمز',
            'country' => 'البلد',
        ],
    ],

    'table' => [
        'columns' => [
            'id'         => 'المعرف',
            'name'       => 'نوع التوظيف',
            'code'       => 'الرمز',
            'country'    => 'البلد',
            'created-by' => 'أنشئ بواسطة',
            'created-at' => 'تاريخ الإنشاء',
            'updated-at' => 'تاريخ التحديث',
        ],

        'filters' => [
            'name'       => 'نوع التوظيف',
            'country'    => 'البلد',
            'created-by' => 'أنشئ بواسطة',
            'updated-at' => 'تاريخ التحديث',
            'created-at' => 'تاريخ الإنشاء',
        ],

        'groups' => [
            'name'       => 'نوع التوظيف',
            'country'    => 'البلد',
            'code'       => 'الرمز',
            'created-by' => 'أنشئ بواسطة',
            'created-at' => 'تاريخ الإنشاء',
            'updated-at' => 'تاريخ التحديث',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'نوع التوظيف',
                    'body'  => 'تم تعديل نوع التوظيف بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف نوع التوظيف',
                    'body'  => 'تم حذف نوع التوظيف بنجاح.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'تم حذف أنواع التوظيف',
                    'body'  => 'تم حذف أنواع التوظيف بنجاح.',
                ],
            ],
        ],

        'empty-state-actions' => [
            'create' => [
                'notification' => [
                    'title' => 'أنواع التوظيف',
                    'body'  => 'تم إنشاء أنواع التوظيف بنجاح.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'name'    => 'نوع التوظيف',
            'code'    => 'الرمز',
            'country' => 'البلد',
        ],
    ],
];
