<?php

return [
    'modal' => [
        'title' => 'ساعات العمل',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'المعلومات العامة',
                'fields' => [
                    'attendance-name' => 'اسم الحضور',
                    'attendance-name' => 'اسم الحضور',
                    'day-of-week'     => 'يوم الأسبوع',
                ],
            ],

            'timing-information' => [
                'title' => 'معلومات التوقيت',

                'fields' => [
                    'day-period' => 'فترات اليوم',
                    'week-type'  => 'نوع الأسبوع',
                    'work-from'  => 'العمل من',
                    'work-to'    => 'العمل إلى',
                ],
            ],

            'date-information' => [
                'title' => 'معلومات التاريخ',

                'fields' => [
                    'starting-date' => 'تاريخ البداية',
                    'ending-date'   => 'تاريخ النهاية',
                ],
            ],

            'additional-information' => [
                'title' => 'معلومات إضافية',

                'fields' => [
                    'durations-days' => 'المدة (أيام)',
                    'display-type'   => 'نوع العرض',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'          => 'اسم الحضور',
            'day-of-week'   => 'يوم الأسبوع',
            'day-period'    => 'فترات اليوم',
            'work-from'     => 'العمل من',
            'work-to'       => 'العمل إلى',
            'starting-date' => 'تاريخ البداية',
            'ending-date'   => 'تاريخ النهاية',
            'display-type'  => 'نوع العرض',
            'created-by'    => 'أنشئ بواسطة',
            'created-at'    => 'تاريخ الإنشاء',
            'updated-at'    => 'تاريخ التحديث',
        ],

        'groups' => [
            'activity-type' => 'نوع النشاط',
            'assignment'    => 'التعيين',
            'assigned-to'   => 'مُعيَّن إلى',
            'interval'      => 'الفاصل الزمني',
            'delay-unit'    => 'وحدة التأخير',
            'delay-from'    => 'التأخير من',
            'created-by'    => 'أنشئ بواسطة',
            'created-at'    => 'تاريخ الإنشاء',
            'updated-at'    => 'تاريخ التحديث',
        ],

        'filters' => [
            'display-type' => 'نوع العرض',
            'day-of-week'  => 'يوم الأسبوع',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'تم تحديث ساعات العمل',
                    'body'  => 'تم تحديث ساعات العمل بنجاح.',
                ],
            ],

            'create' => [
                'notification' => [
                    'title' => 'تم إنشاء ساعات العمل',
                    'body'  => 'تم إنشاء ساعات العمل بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف ساعات العمل',
                    'body'  => 'تم حذف ساعات العمل بنجاح.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'تم استعادة ساعات العمل',
                    'body'  => 'تم استعادة ساعات العمل بنجاح.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'تم حذف ساعات العمل',
                    'body'  => 'تم حذف ساعات العمل بنجاح.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'تم استعادة ساعات العمل',
                    'body'  => 'تم استعادة ساعات العمل بنجاح.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'تم حذف ساعات العمل نهائياً',
                    'body'  => 'تم حذف ساعات العمل نهائياً بنجاح.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'المعلومات العامة',

                'entries' => [
                    'name'        => 'اسم الحضور',
                    'day-of-week' => 'يوم الأسبوع',
                ],
            ],

            'timing-information' => [
                'title' => 'معلومات التوقيت',

                'entries' => [
                    'day-period' => 'فترات اليوم',
                    'week-type'  => 'نوع الأسبوع',
                    'work-from'  => 'العمل من',
                    'work-to'    => 'العمل إلى',
                ],
            ],

            'date-information' => [
                'title' => 'معلومات التاريخ',

                'entries' => [
                    'starting-date' => 'تاريخ البداية',
                    'ending-date'   => 'تاريخ النهاية',
                ],
            ],

            'additional-information' => [
                'title' => 'معلومات إضافية',

                'entries' => [
                    'durations-days' => 'المدة (أيام)',
                    'display-type'   => 'نوع العرض',
                ],
            ],
        ],

        'note' => 'ملاحظة',
    ],
];
