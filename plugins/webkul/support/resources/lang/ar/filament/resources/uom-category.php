<?php

return [
    'navigation' => [
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
                    'name'     => 'وحدة القياس',
                    'ratio'    => 'النسبة',
                    'rounding' => 'دقة التقريب',
                ],

                'validations' => [
                    'missing-reference'          => 'يجب أن تحتوي هذه الفئة على وحدة قياس مرجعية.',
                    'multiple-references'        => 'يجب أن تحتوي هذه الفئة على وحدة قياس مرجعية واحدة فقط.',
                    'ratio-greater-than-zero'    => 'لا يمكن أن تكون نسبة التحويل لوحدة القياس صفرًا.',
                    'rounding-greater-than-zero' => 'يجب أن تكون دقة التقريب أكبر من صفر.',
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
            'uoms'       => 'الوحدات',
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
