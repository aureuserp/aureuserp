<?php

return [
    'title' => 'جداول الوقت',

    'form' => [
        'date'                   => 'التاريخ',
        'employee'               => 'الموظف',
        'description'            => 'الوصف',
        'time-spent'             => 'الوقت المستغرق',
        'time-spent-helper-text' => 'الوقت المستغرق بالساعات (مثال: 1.5 ساعة تعني ساعة و30 دقيقة)',
    ],

    'table' => [
        'header-actions' => [
            'create' => [
                'label' => 'إضافة جدول وقت',

                'notification' => [
                    'title' => 'تم إنشاء جدول الوقت',
                    'body'  => 'تم إنشاء جدول الوقت بنجاح.',
                ],
            ],
        ],

        'columns' => [
            'date'                   => 'التاريخ',
            'employee'               => 'الموظف',
            'description'            => 'الوصف',
            'time-spent'             => 'الوقت المستغرق',
            'time-spent-on-subtasks' => 'الوقت المستغرق على المهام الفرعية',
            'total-time-spent'       => 'إجمالي الوقت المستغرق',
            'remaining-time'         => 'الوقت المتبقي',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'تم تحديث جدول الوقت',
                    'body'  => 'تم تحديث جدول الوقت بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف جدول الوقت',
                    'body'  => 'تم حذف جدول الوقت بنجاح.',
                ],
            ],
        ],
    ],
];
