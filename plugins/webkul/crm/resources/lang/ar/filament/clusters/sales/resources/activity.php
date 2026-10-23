<?php

return [
    'model-label'        => 'النشاط',
    'plural-model-label' => 'الأنشطة',

    'navigation' => [
        'title' => 'أنشطتي',
    ],

    'views' => [
        'list'   => 'قائمة',
        'kanban' => 'كانبان',
        'graph'  => 'رسم بياني',
    ],

    'table' => [
        'columns' => [
            'due-date'      => 'تاريخ الاستحقاق',
            'opportunity'   => 'عميل محتمل / فرصة',
            'customer'      => 'العميل',
            'activity-type' => 'نوع النشاط',
            'summary'       => 'الملخص',
            'assigned-to'   => 'مسند إلى',
            'stage'         => 'المرحلة',
            'created-at'    => 'تاريخ الإنشاء',
        ],

        'groups' => [
            'activity-type' => 'نوع النشاط',
            'assigned-to'   => 'مسند إلى',
            'due-date'      => 'تاريخ الاستحقاق',
        ],

        'filters' => [
            'due-status'    => 'الاستحقاق',
            'activity-type' => 'نوع النشاط',
            'assigned-to'   => 'مسند إلى',
        ],

        'empty-state' => [
            'heading'     => 'لا توجد أنشطة',
            'description' => 'استخدم جدولة نشاط، أو أيقونة الساعة على الفرصة، لتخطيط خطوتك التالية.',
        ],
    ],

    'actions' => [
        'schedule' => [
            'label' => 'جدولة نشاط',

            'modal' => [
                'heading' => 'جدولة نشاط',
                'submit'  => 'جدولة',
            ],

            'form' => [
                'opportunity'   => 'الفرصة',
                'activity-type' => 'نوع النشاط',
                'due-date'      => 'تاريخ الاستحقاق',
                'summary'       => 'الملخص',
                'assigned-to'   => 'مسند إلى',
                'note'          => 'ملاحظة',
            ],

            'notification' => [
                'success' => [
                    'title' => 'تمت جدولة النشاط',
                    'body'  => 'تمت جدولة النشاط على ":opportunity".',
                ],

                'forbidden' => [
                    'title' => 'غير مسموح',
                    'body'  => 'ليس لديك صلاحية لجدولة أنشطة على هذه الفرصة.',
                ],
            ],
        ],

        'open' => [
            'label' => 'فتح السجل',
        ],

        'done' => [
            'label' => 'تحديد كمنجز',

            'modal' => [
                'heading' => 'تحديد النشاط كمنجز',
                'submit'  => 'تم',
            ],

            'form' => [
                'feedback' => 'الملاحظات',
            ],

            'notification' => [
                'title' => 'تم إنجاز النشاط',
                'body'  => 'تم تحديد النشاط كمنجز وتسجيله في الفرصة.',
            ],
        ],

        'reschedule' => [
            'label' => 'إعادة جدولة',

            'modal' => [
                'heading' => 'إعادة جدولة النشاط',
            ],

            'form' => [
                'due-date'    => 'تاريخ الاستحقاق',
                'summary'     => 'الملخص',
                'assigned-to' => 'مسند إلى',
            ],

            'notification' => [
                'title' => 'تمت إعادة جدولة النشاط',
                'body'  => 'تم تحديث النشاط.',
            ],
        ],

        'cancel' => [
            'label'      => 'إلغاء النشاط',
            'bulk-label' => 'إلغاء الأنشطة',

            'modal' => [
                'heading' => 'إلغاء النشاط',
            ],

            'notification' => [
                'title' => 'تم إلغاء النشاط',
                'body'  => 'تمت إزالة النشاط.',
            ],
        ],
    ],
];
