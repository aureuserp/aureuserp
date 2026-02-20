<?php

return [
    'navigation' => [
        'group' => 'الإعدادات',
        'title' => 'فئات وحدات القياس',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'عام',

                'fields' => [
                    'name' => 'الاسم',
                ],
            ],

            'uoms' => [
                'title' => 'وحدات القياس',

                'fields' => [
                    'uoms'     => 'الوحدات',
                    'type'     => 'النوع',
                    'name'     => 'الاسم',
                    'factor'   => 'معامل التحويل',
                    'rounding' => 'دقة التقريب',
                ],

                'actions' => [
                    'add' => 'إضافة وحدة',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'الاسم',
            'uoms-count' => 'الوحدات',
            'created-at' => 'تاريخ الإنشاء',
            'updated-at' => 'تاريخ التحديث',
        ],

        'groups' => [
            'created-at' => 'تاريخ الإنشاء',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'تم تحديث فئة وحدة القياس',
                    'body'  => 'تم تحديث فئة وحدة القياس بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف فئة وحدة القياس',
                    'body'  => 'تم حذف فئة وحدة القياس بنجاح.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'تم حذف فئات وحدات القياس',
                    'body'  => 'تم حذف فئات وحدات القياس بنجاح.',
                ],
            ],
        ],
    ],
];
