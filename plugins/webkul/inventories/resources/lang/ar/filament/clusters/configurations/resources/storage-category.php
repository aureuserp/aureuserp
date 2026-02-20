<?php

return [
    'navigation' => [
        'title' => 'فئات التخزين',
        'group' => 'إدارة المستودعات',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'عام',

                'fields' => [
                    'name'               => 'الاسم',
                    'allow-new-products' => 'السماح بمنتجات جديدة',
                    'max-weight'         => 'الوزن الأقصى',
                    'company'            => 'الشركة',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'               => 'الاسم',
            'allow-new-products' => 'السماح بمنتجات جديدة',
            'max-weight'         => 'الوزن الأقصى',
            'company'            => 'الشركة',
            'deleted-at'         => 'تاريخ الحذف',
            'created-at'         => 'تاريخ الإنشاء',
            'updated-at'         => 'تاريخ التحديث',
        ],

        'groups' => [
            'allow-new-products' => 'السماح بمنتجات جديدة',
            'created-at'         => 'تاريخ الإنشاء',
            'updated-at'         => 'تاريخ التحديث',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'تم حذف فئة التخزين',
                    'body'  => 'تم حذف فئة التخزين بنجاح.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'تم حذف فئات التخزين',
                    'body'  => 'تم حذف فئات التخزين بنجاح.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'  => 'عام',

                'entries' => [
                    'name'               => 'الاسم',
                    'allow-new-products' => 'السماح بمنتجات جديدة',
                    'max-weight'         => 'الوزن الأقصى',
                    'company'            => 'الشركة',
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
