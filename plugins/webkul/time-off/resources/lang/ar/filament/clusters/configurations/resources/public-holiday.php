<?php

return [
    'title' => 'العطل الرسمية',

    'model-label' => 'عطلة رسمية',

    'navigation' => [
        'title' => 'العطل الرسمية',
    ],

    'form' => [
        'fields' => [
            'name'             => 'الاسم',
            'name-placeholder' => 'أدخل اسم العطلة الرسمية',
            'date-from'        => 'تاريخ البداية',
            'date-to'          => 'تاريخ النهاية',
            'color'            => 'اللون',
            'calendar'         => 'التقويم',
        ],
    ],

    'table' => [
        'columns' => [
            'name'         => 'الاسم',
            'company-name' => 'اسم الشركة',
            'calendar'     => 'التقويم',
            'created-by'   => 'أنشئ بواسطة',
            'date-from'    => 'تاريخ البداية',
            'date-to'      => 'تاريخ النهاية',
        ],

        'filters' => [
            'name'         => 'الاسم',
            'company-name' => 'اسم الشركة',
            'created-by'   => 'أنشئ بواسطة',
            'date-from'    => 'تاريخ البداية',
            'date-to'      => 'تاريخ النهاية',
            'created-at'   => 'تاريخ الإنشاء',
            'updated-at'   => 'تاريخ التحديث',
        ],

        'groups' => [
            'name'         => 'الاسم',
            'company-name' => 'اسم الشركة',
            'created-by'   => 'أنشئ بواسطة',
            'date-from'    => 'تاريخ البداية',
            'date-to'      => 'تاريخ النهاية',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'تم تحديث العطلة الرسمية',
                    'body'  => 'تم تحديث العطلة الرسمية بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف العطلة الرسمية',
                    'body'  => 'تم حذف العطلة الرسمية بنجاح.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'تم حذف العطل الرسمية',
                    'body'  => 'تم حذف العطل الرسمية بنجاح.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'name'      => 'الاسم',
            'date-from' => 'تاريخ البداية',
            'date-to'   => 'تاريخ النهاية',
            'color'     => 'اللون',
        ],
    ],
];
