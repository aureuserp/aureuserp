<?php

return [
    'form' => [
        'partner' => 'الشريك',
        'name'    => 'الاسم',
        'email'   => 'البريد الإلكتروني',
        'phone'   => 'الهاتف',
        'mobile'  => 'الجوال',
        'type'    => 'النوع',
        'address' => 'العنوان',
        'city'    => 'المدينة',
        'street1' => 'الشارع 1',
        'street2' => 'الشارع 2',
        'state'   => 'الولاية',
        'zip'     => 'الرمز البريدي',
        'code'    => 'الرمز',
        'country' => 'الدولة',
    ],

    'table' => [
        'header-actions' => [
            'create' => [
                'label' => 'إضافة عنوان',

                'notification' => [
                    'title' => 'تم إنشاء العنوان',
                    'body'  => 'تم إنشاء العنوان بنجاح.',
                ],
            ],
        ],

        'columns' => [
            'type'    => 'النوع',
            'name'    => 'اسم جهة الاتصال',
            'address' => 'العنوان',
            'city'    => 'المدينة',
            'street1' => 'الشارع 1',
            'street2' => 'الشارع 2',
            'state'   => 'الولاية',
            'zip'     => 'الرمز البريدي',
            'country' => 'الدولة',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'تم تحديث العنوان',
                    'body'  => 'تم تحديث العنوان بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف العنوان',
                    'body'  => 'تم حذف العنوان بنجاح.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'تم حذف العناوين',
                    'body'  => 'تم حذف العناوين بنجاح.',
                ],
            ],
        ],
    ],
];
