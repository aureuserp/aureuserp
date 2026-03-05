<?php

return [
    'title' => 'الإجازات',

    'model-label' => 'إجازة',

    'navigation' => [
        'title' => 'الإجازات',
    ],

    'form' => [
        'fields' => [
            'employee-name'     => 'اسم الموظف',
            'department-name'   => 'اسم القسم',
            'time-off-type'     => 'نوع الإجازة',
            'date'              => 'التاريخ',
            'dates'             => 'التواريخ',
            'request-date-from' => 'تاريخ الطلب من',
            'request-date-to'   => 'تاريخ الطلب إلى',
            'description'       => 'الوصف',
            'period'            => 'الفترة',
            'half-day'          => 'نصف يوم',
            'requested-days'    => 'المطلوب (أيام/ساعات)',
            'description'       => 'الوصف',
            'attachment'        => 'المرفق',
            'day'               => ':day يوم',
            'days'              => ':days يوم/أيام',
        ],
    ],

    'table' => [
        'columns' => [
            'employee-name'  => 'الموظف',
            'time-off-type'  => 'نوع الإجازة',
            'description'    => 'الوصف',
            'date-from'      => 'التاريخ من',
            'date-to'        => 'التاريخ إلى',
            'duration'       => 'المدة',
            'status'         => 'الحالة',
        ],

        'groups' => [
            'employee-name' => 'الموظف',
            'time-off-type' => 'نوع الإجازة',
            'status'        => 'الحالة',
            'start-date'    => 'تاريخ البداية',
            'start-to'      => 'تاريخ النهاية',
            'updated-at'    => 'تاريخ التحديث',
            'created-at'    => 'تاريخ الإنشاء',
        ],

        'actions' => [
            'approve' => [
                'title' => [
                    'validate' => 'تحقق',
                    'approve'  => 'موافقة',
                ],
                'notification' => [
                    'title' => 'تمت الموافقة على الإجازة',
                    'body'  => 'تمت الموافقة على الإجازة بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف الإجازة',
                    'body'  => 'تم حذف الإجازة بنجاح.',
                ],
            ],

            'refused' => [
                'title'        => 'رفض',
                'notification' => [
                    'title' => 'تم رفض الإجازة',
                    'body'  => 'تم رفض الإجازة بنجاح.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'تم حذف الإجازة',
                    'body'  => 'تم حذف الإجازة بنجاح.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'employee-name'     => 'اسم الموظف',
            'department-name'   => 'اسم القسم',
            'time-off-type'     => 'نوع الإجازة',
            'date'              => 'التاريخ',
            'dates'             => 'التواريخ',
            'request-date-from' => 'تاريخ الطلب من',
            'request-date-to'   => 'تاريخ الطلب إلى',
            'description'       => 'الوصف',
            'period'            => 'الفترة',
            'half-day'          => 'نصف يوم',
            'requested-days'    => 'المطلوب (أيام/ساعات)',
            'attachment'        => 'المرفق',
            'day'               => ':day يوم',
            'days'              => ':days يوم/أيام',
            'date-from'         => 'التاريخ من',
            'date-to'           => 'التاريخ إلى',
            'status'            => 'الحالة',
        ],
    ],
];
