<?php

return [
    'navigation' => [
        'title' => 'الطرود',
        'group' => 'المخزون',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'عام',

                'fields' => [
                    'name'             => 'الاسم',
                    'name-placeholder' => 'مثال: PACK007',
                    'package-type'     => 'نوع الطرد',
                    'pack-date'        => 'تاريخ التعبئة',
                    'location'         => 'الموقع',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'         => 'الاسم',
            'package-type' => 'نوع الطرد',
            'location'     => 'الموقع',
            'company'      => 'الشركة',
            'created-at'   => 'تاريخ الإنشاء',
            'updated-at'   => 'تاريخ التحديث',
        ],

        'groups' => [
            'package-type'   => 'نوع الطرد',
            'location'       => 'الموقع',
            'created-at'     => 'تاريخ الإنشاء',
        ],

        'filters' => [
            'package-type' => 'نوع الطرد',
            'location'     => 'الموقع',
            'creator'      => 'المُنشئ',
            'company'      => 'الشركة',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'تم حذف الطرد',
                        'body'  => 'تم حذف الطرد بنجاح.',
                    ],

                    'error' => [
                        'title' => 'تعذر حذف الطرد',
                        'body'  => 'لا يمكن حذف الطرد لأنه قيد الاستخدام حالياً.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'print-without-content' => [
                'label' => 'طباعة الباركود',
            ],

            'print-with-content' => [
                'label' => 'طباعة الباركود مع المحتوى',
            ],

            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'تم حذف الطرود',
                        'body'  => 'تم حذف الطرود بنجاح.',
                    ],

                    'error' => [
                        'title' => 'تعذر حذف الطرود',
                        'body'  => 'لا يمكن حذف الطرود لأنها قيد الاستخدام حالياً.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'تفاصيل الطرد',

                'entries' => [
                    'name'         => 'اسم الطرد',
                    'package-type' => 'نوع الطرد',
                    'pack-date'    => 'تاريخ التعبئة',
                    'location'     => 'الموقع',
                    'company'      => 'الشركة',
                    'created-at'   => 'تاريخ الإنشاء',
                    'updated-at'   => 'آخر تحديث',
                ],
            ],

            'record-information' => [
                'title' => 'معلومات السجل',

                'entries' => [
                    'created-by'   => 'أنشئ بواسطة',
                    'created-at'   => 'تاريخ الإنشاء',
                    'last-updated' => 'آخر تحديث',
                ],
            ],
        ],
    ],
];
