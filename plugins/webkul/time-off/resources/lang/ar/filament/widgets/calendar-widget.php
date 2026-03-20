<?php

return [
    'heading' => [
        'title' => 'طلبات الإجازات',
    ],

    'modal-actions' => [
        'edit' => [
            'title'                         => 'تعديل',
            'duration-display'              => ':count يوم عمل|:count أيام عمل',
            'duration-display-with-weekend' => ':count يوم عمل (+ :weekend يوم عطلة)|:count أيام عمل (+ :weekend أيام عطلة)',

            'notification' => [
                'title' => 'تم تحديث الإجازة',
                'body'  => 'تم تحديث طلب الإجازة بنجاح.',
            ],
        ],

        'delete' => [
            'title' => 'حذف',
        ],
    ],

    'view-action' => [
        'title'       => 'عرض',
        'description' => 'عرض طلب الإجازة',
    ],

    'header-actions' => [
        'create' => [
            'title'       => 'إجازة جديدة',
            'description' => 'إنشاء طلب إجازة',

            'notification' => [
                'title' => 'تم إنشاء الإجازة',
                'body'  => 'تم إنشاء طلب الإجازة بنجاح.',
            ],

            'employee-not-found' => [
                'notification' => [
                    'title' => 'لم يتم العثور على الموظف',
                    'body'  => 'يرجى إضافة موظف إلى ملفك الشخصي قبل إنشاء طلب إجازة.',
                ],
            ],

            'success' => [
                'notification' => [
                    'title' => 'تم إنشاء الإجازة',
                    'body'  => 'تم إنشاء طلب الإجازة بنجاح.',
                ],
            ],
        ],
    ],

    'form' => [
        'title'       => 'طلب إجازة',
        'description' => 'أنشئ أو عدّل طلب الإجازة الخاص بك بالتفاصيل التالية:',

        'fields' => [
            'time-off-type'             => 'نوع الإجازة',
            'time-off-type-placeholder' => 'اختر نوع الإجازة',
            'time-off-type-helper'      => 'اختر نوع الإجازة التي تطلبها.',
            'request-date-from'         => 'تاريخ بداية الطلب',
            'request-date-to'           => 'تاريخ نهاية الطلب',
            'period'                    => 'الفترة',
            'half-day'                  => 'نصف يوم',
            'half-day-helper'           => 'تفعيل لإجازة نصف يوم.',
            'requested-days'            => 'المطلوب (أيام/ساعات)',
            'description'               => 'الوصف',
            'description-placeholder'   => 'لم يتم تقديم وصف',
            'description-helper'        => 'قدم وصفاً موجزاً لطلب الإجازة الخاص بك.',
            'duration'                  => 'المدة',
            'please-select-dates'       => 'يرجى تحديد تاريخ بداية ونهاية الطلب.',
        ],
    ],

    'infolist' => [
        'title'       => 'تفاصيل الإجازة',
        'description' => 'إليك تفاصيل طلب الإجازة الخاص بك:',
        'entries'     => [
            'time-off-type'           => 'نوع الإجازة',
            'request-date-from'       => 'تاريخ بداية الطلب',
            'request-date-to'         => 'تاريخ نهاية الطلب',
            'description'             => 'الوصف',
            'description-placeholder' => 'لم يتم تقديم وصف',
            'duration'                => 'المدة',
            'status'                  => 'الحالة',
        ],
    ],

    'events' => [
        'title' => ':name في حالة :status: :days يوم/أيام',
    ],
];
