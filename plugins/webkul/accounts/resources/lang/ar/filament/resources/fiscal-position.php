<?php

return [
    'global-search' => [
        'zip-from' => 'الرمز البريدي من',
        'zip-to'   => 'الرمز البريدي إلى',
        'name'     => 'الاسم',
    ],

    'form' => [
        'fields' => [
            'name'                 => 'الاسم',
            'foreign-vat'          => 'ضريبة القيمة المضافة الأجنبية',
            'country'              => 'البلد',
            'country-group'        => 'مجموعة البلدان',
            'zip-from'             => 'الرمز البريدي من',
            'zip-to'               => 'الرمز البريدي إلى',
            'detect-automatically' => 'اكتشاف تلقائي',
            'notes'                => 'ملاحظات',
        ],
    ],

    'table' => [
        'columns' => [
            'name'                 => 'الاسم',
            'company'              => 'الشركة',
            'country'              => 'البلد',
            'country-group'        => 'مجموعة البلدان',
            'created-by'           => 'أنشئ بواسطة',
            'zip-from'             => 'الرمز البريدي من',
            'zip-to'               => 'الرمز البريدي إلى',
            'status'               => 'الحالة',
            'detect-automatically' => 'اكتشاف تلقائي',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'تم حذف شروط الدفع',
                    'body'  => 'تم حذف شروط الدفع بنجاح.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'تم حذف المركز المالي',
                    'body'  => 'تم حذف المركز المالي بنجاح.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'name'                 => 'الاسم',
            'foreign-vat'          => 'ضريبة القيمة المضافة الأجنبية',
            'country'              => 'البلد',
            'country-group'        => 'مجموعة البلدان',
            'zip-from'             => 'الرمز البريدي من',
            'zip-to'               => 'الرمز البريدي إلى',
            'detect-automatically' => 'اكتشاف تلقائي',
            'notes'                => 'ملاحظات',
        ],
    ],
];
