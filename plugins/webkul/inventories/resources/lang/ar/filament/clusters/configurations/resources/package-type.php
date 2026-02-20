<?php

return [
    'navigation' => [
        'title' => 'أنواع الطرود',
        'group' => 'التوصيل',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'عام',

                'fields' => [
                    'name'       => 'الاسم',
                    'barcode'    => 'الباركود',
                    'company'    => 'الشركة',
                    'weight'     => 'الوزن',
                    'max-weight' => 'الوزن الأقصى',

                    'fieldsets' => [
                        'size' => [
                            'title' => 'الحجم',

                            'fields' => [
                                'length' => 'الطول',
                                'width'  => 'العرض',
                                'height' => 'الارتفاع',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'الاسم',
            'barcode'    => 'الباركود',
            'weight'     => 'الوزن',
            'max-weight' => 'الوزن الأقصى',
            'width'      => 'العرض',
            'height'     => 'الارتفاع',
            'length'     => 'الطول',
            'created-at' => 'تاريخ الإنشاء',
            'updated-at' => 'تاريخ التحديث',
        ],

        'groups' => [
            'created-at' => 'تاريخ الإنشاء',
            'updated-at' => 'تاريخ التحديث',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'تم حذف نوع الطرد',
                    'body'  => 'تم حذف نوع الطرد بنجاح.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'تم حذف نوع الطرد',
                    'body'  => 'تم حذف نوع الطرد بنجاح.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'   => 'معلومات عامة',
                'entries' => [
                    'name'      => 'الاسم',
                    'fieldsets' => [
                        'size' => [
                            'title'   => 'أبعاد الطرد',
                            'entries' => [
                                'length' => 'الطول',
                                'width'  => 'العرض',
                                'height' => 'الارتفاع',
                            ],
                        ],
                    ],
                    'weight'     => 'الوزن الأساسي',
                    'max-weight' => 'الوزن الأقصى',
                    'barcode'    => 'الباركود',
                    'company'    => 'الشركة',
                    'created-at' => 'تاريخ الإنشاء',
                    'updated-at' => 'آخر تحديث',
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
