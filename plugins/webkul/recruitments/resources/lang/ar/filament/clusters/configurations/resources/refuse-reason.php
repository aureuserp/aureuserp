<?php

return [
    'title' => 'سبب الرفض',

    'navigation' => [
        'title' => 'أسباب الرفض',
        'group' => 'الطلبات',
    ],

    'form' => [
        'fields' => [
            'name'             => 'الاسم',
            'template'         => [
                'title'                    => 'القالب',
                'applicant-refuse'         => 'رفض المتقدم',
                'applicant-not-interested' => 'المتقدم غير مهتم',
            ],
            'name-placeholder' => 'أدخل اسم سبب الرفض',
        ],
    ],

    'table' => [
        'columns' => [
            'id'         => 'المعرف',
            'name'       => 'الاسم',
            'template'   => 'القالب',
            'created-by' => 'أنشئ بواسطة',
            'created-at' => 'تاريخ الإنشاء',
            'updated-at' => 'تاريخ التحديث',
        ],

        'filters' => [
            'name'       => 'الاسم',
            'employee'   => 'الموظف',
            'created-by' => 'أنشئ بواسطة',
            'updated-at' => 'تاريخ التحديث',
            'created-at' => 'تاريخ الإنشاء',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'تم تحديث سبب الرفض',
                    'body'  => 'تم تحديث سبب الرفض بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف سبب الرفض',
                    'body'  => 'تم حذف سبب الرفض بنجاح.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'تم حذف أسباب الرفض',
                    'body'  => 'تم حذف أسباب الرفض بنجاح.',
                ],
            ],
        ],

        'empty-state-action' => [
            'create' => [
                'notification' => [
                    'title' => 'تم إنشاء سبب الرفض',
                    'body'  => 'تم إنشاء سبب الرفض بنجاح.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'name'       => 'الاسم',
        'template'   => 'القالب',
    ],
];
