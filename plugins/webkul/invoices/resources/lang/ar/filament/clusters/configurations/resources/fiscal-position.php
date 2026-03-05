<?php

return [
    'title' => 'المواقف المالية',

    'navigation' => [
        'title' => 'المواقف المالية',
        'group' => 'المحاسبة',
    ],

    'global-search' => [
        'zip-from' => 'الرمز البريدي من',
        'zip-to'   => 'الرمز البريدي إلى',
        'name'     => 'الاسم',
    ],

    'form' => [
        'fields' => [
            'name'                 => 'الاسم',
            'foreign-vat'          => 'ضريبة القيمة المضافة الأجنبية',
            'country'              => 'الدولة',
            'country-group'        => 'مجموعة الدول',
            'zip-from'             => 'الرمز البريدي من',
            'zip-to'               => 'الرمز البريدي إلى',
            'detect-automatically' => 'الكشف التلقائي',
            'notes'                => 'ملاحظات',
        ],
    ],

    'table' => [
        'columns' => [
            'name'                 => 'الاسم',
            'company'              => 'الشركة',
            'country'              => 'الدولة',
            'country-group'        => 'مجموعة الدول',
            'created-by'           => 'أنشئ بواسطة',
            'zip-from'             => 'الرمز البريدي من',
            'zip-to'               => 'الرمز البريدي إلى',
            'status'               => 'الحالة',
            'detect-automatically' => 'الكشف التلقائي',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'تم حذف الموقف المالي',
                    'body'  => 'تم حذف الموقف المالي بنجاح.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'تم حذف الموقف المالي',
                    'body'  => 'تم حذف الموقف المالي بنجاح.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'name'                 => 'الاسم',
            'foreign-vat'          => 'ضريبة القيمة المضافة الأجنبية',
            'country'              => 'الدولة',
            'country-group'        => 'مجموعة الدول',
            'zip-from'             => 'الرمز البريدي من',
            'zip-to'               => 'الرمز البريدي إلى',
            'detect-automatically' => 'الكشف التلقائي',
            'notes'                => 'ملاحظات',
        ],
    ],
];
