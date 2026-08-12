<?php

return [
    'title' => 'جداول العمل',

    'navigation' => [
        'title' => 'جداول العمل',
        'group' => 'الموظف',
    ],

    'groups' => [
        'status'     => 'الحالة',
        'created-by' => 'أنشئ بواسطة',
        'created-at' => 'تاريخ الإنشاء',
        'updated-at' => 'تاريخ التحديث',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'معلومات عامة',
                'fields' => [
                    'name'                  => 'الاسم',
                    'schedule-name'         => 'اسم الجدول',
                    'schedule-name-tooltip' => 'يرجى كتابة اسم وصفي لجدول العمل.',
                    'timezone'              => 'المنطقة الزمنية',
                    'timezone-tooltip'      => 'يرجى تحديد المنطقة الزمنية لجدول العمل.',
                    'company'               => 'الشركة',
                ],
            ],

            'configuration' => [
                'title'  => 'تكوين ساعات العمل',
                'fields' => [
                    'hours-per-day'                   => 'ساعات في اليوم',
                    'hours-per-day-suffix'            => 'ساعات',
                    'full-time-required-hours'        => 'ساعات الدوام الكامل المطلوبة',
                    'full-time-required-hours-suffix' => 'ساعات في الأسبوع',
                ],
            ],

            'flexibility' => [
                'title'  => 'المرونة',
                'fields' => [
                    'status'                     => 'الحالة',
                    'two-weeks-calendar'         => 'جدول أسبوعين',
                    'two-weeks-calendar-tooltip' => 'تمكين جدول عمل متناوب لأسبوعين.',
                    'flexible-hours'             => 'ساعات مرنة',
                    'flexible-hours-tooltip'     => 'السماح للموظفين بساعات عمل مرنة.',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'id'             => 'المعرف',
            'name'           => 'اسم الجدول',
            'timezone'       => 'المنطقة الزمنية',
            'company'        => 'الشركة',
            'flexible-hours' => 'ساعات مرنة',
            'status'         => 'الحالة',
            'daily-hours'    => 'الساعات اليومية',
            'created-by'     => 'أنشئ بواسطة',
            'created-at'     => 'تاريخ الإنشاء',
            'updated-at'     => 'تاريخ التحديث',
        ],

        'filters' => [
            'company'           => 'الشركة',
            'is-active'         => 'الحالة',
            'two-week-calendar' => 'جدول أسبوعين',
            'flexible-hours'    => 'ساعات مرنة',
            'timezone'          => 'المنطقة الزمنية',
            'name'              => 'اسم الجدول',
            'attendance'        => 'الحضور',
            'created-by'        => 'أنشئ بواسطة',
            'daily-hours'       => 'الساعات اليومية',
            'updated-at'        => 'تاريخ التحديث',
            'created-at'        => 'تاريخ الإنشاء',
        ],

        'groups' => [
            'name'           => 'اسم الجدول',
            'status'         => 'الحالة',
            'timezone'       => 'المنطقة الزمنية',
            'flexible-hours' => 'ساعات مرنة',
            'daily-hours'    => 'الساعات اليومية',
            'created-by'     => 'أنشئ بواسطة',
            'created-at'     => 'تاريخ الإنشاء',
            'updated-at'     => 'تاريخ التحديث',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'تم استعادة جدول العمل',
                    'body'  => 'تم استعادة جدول العمل بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف جدول العمل',
                    'body'  => 'تم حذف جدول العمل بنجاح.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'تم حذف جدول العمل نهائياً',
                    'body'  => 'تم حذف جدول العمل نهائياً بنجاح.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'تم استعادة جداول العمل',
                    'body'  => 'تم استعادة جداول العمل بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف جداول العمل',
                    'body'  => 'تم حذف جداول العمل بنجاح.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'تم حذف جداول العمل نهائياً',
                    'body'  => 'تم حذف جداول العمل نهائياً بنجاح.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'   => 'معلومات عامة',
                'entries' => [
                    'name'                  => 'الاسم',
                    'schedule-name'         => 'اسم الجدول',
                    'schedule-name-tooltip' => 'يرجى كتابة اسم وصفي لجدول العمل.',
                    'timezone'              => 'المنطقة الزمنية',
                    'timezone-tooltip'      => 'يرجى تحديد المنطقة الزمنية لجدول العمل.',
                    'company'               => 'الشركة',
                ],
            ],

            'configuration' => [
                'title'   => 'تكوين ساعات العمل',
                'entries' => [
                    'hours-per-day'                   => 'ساعات في اليوم',
                    'hours-per-day-suffix'            => 'ساعات',
                    'full-time-required-hours'        => 'ساعات الدوام الكامل المطلوبة',
                    'full-time-required-hours-suffix' => 'ساعات في الأسبوع',
                ],
            ],

            'flexibility' => [
                'title'   => 'المرونة',
                'entries' => [
                    'status'                     => 'الحالة',
                    'two-weeks-calendar'         => 'جدول أسبوعين',
                    'two-weeks-calendar-tooltip' => 'تمكين جدول عمل متناوب لأسبوعين.',
                    'flexible-hours'             => 'ساعات مرنة',
                    'flexible-hours-tooltip'     => 'السماح للموظفين بساعات عمل مرنة.',
                ],
            ],
        ],
    ],
];
