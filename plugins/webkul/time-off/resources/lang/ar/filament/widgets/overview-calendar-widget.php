<?php

return [
    'heading' => [
        'title' => 'نظرة عامة على الإجازات',
    ],

    'modal-actions' => [
        'edit' => [
            'title' => 'تعديل',
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
        ],
    ],

    'form' => [
        'fields' => [
            'time-off-type'     => 'نوع الإجازة',
            'request-date-from' => 'تاريخ بداية الطلب',
            'request-date-to'   => 'تاريخ نهاية الطلب',
            'period'            => 'الفترة',
            'half-day'          => 'نصف يوم',
            'requested-days'    => 'المطلوب (أيام/ساعات)',
            'description'       => 'الوصف',
        ],
    ],

    'infolist' => [
        'entries' => [
            'time-off-type'           => 'نوع الإجازة',
            'request-date-from'       => 'تاريخ بداية الطلب',
            'request-date-to'         => 'تاريخ نهاية الطلب',
            'description'             => 'الوصف',
            'description-placeholder' => 'لم يتم تقديم وصف',
            'duration'                => 'المدة',
            'status'                  => 'الحالة',
        ],
    ],
];
