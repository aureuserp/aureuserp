<?php

return [
    'title' => 'تخصيصاتي',

    'model-label' => 'تخصيصاتي',

    'navigation' => [
        'title' => 'تخصيصاتي',
    ],

    'form' => [
        'fields' => [
            'name'                => 'الاسم',
            'name-placeholder'    => 'نوع الإجازة (من بداية الصلاحية إلى نهاية الصلاحية/بدون حد)',
            'time-off-type'       => 'نوع الإجازة',
            'allocation-type'     => 'نوع التخصيص',
            'validity-period'     => 'فترة الصلاحية',
            'date-from'           => 'التاريخ من',
            'date-to'             => 'التاريخ إلى',
            'date-to-placeholder' => 'بدون حد',
            'allocation'          => 'التخصيص',
            'allocation-suffix'   => 'عدد الأيام',
            'reason'              => 'السبب',
        ],
    ],

    'table' => [
        'columns' => [
            'time-off-type'   => 'نوع الإجازة',
            'amount'          => 'الكمية',
            'allocation-type' => 'نوع التخصيص',
            'status'          => 'الحالة',
        ],

        'groups' => [
            'time-off-type'   => 'نوع الإجازة',
            'employee-name'   => 'اسم الموظف',
            'allocation-type' => 'نوع التخصيص',
            'status'          => 'الحالة',
            'start-date'      => 'تاريخ البداية',
        ],

        'actions' => [
            'approve' => [
                'title' => [
                    'validate' => 'تحقق',
                    'approve'  => 'موافقة',
                ],
                'notification' => [
                    'title' => 'تمت الموافقة على التخصيص',
                    'body'  => 'تمت الموافقة على التخصيص بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف التخصيص',
                    'body'  => 'تم حذف التخصيص بنجاح.',
                ],
            ],

            'refused' => [
                'title'        => 'رفض',
                'notification' => [
                    'title' => 'تم رفض التخصيص',
                    'body'  => 'تم رفض التخصيص بنجاح.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'تم حذف التخصيصات',
                    'body'  => 'تم حذف التخصيصات بنجاح.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'allocation-details' => [
                'title'   => 'تفاصيل التخصيص',
                'entries' => [
                    'name'                => 'الاسم',
                    'time-off-type'       => 'نوع الإجازة',
                    'allocation-type'     => 'نوع التخصيص',
                ],
            ],

            'validity-period' => [
                'title'   => 'فترة الصلاحية',
                'entries' => [
                    'date-from' => 'التاريخ من',
                    'date-to'   => 'التاريخ إلى',
                    'reason'    => 'السبب',
                ],
            ],
            'allocation-status' => [
                'title'   => 'حالة التخصيص',
                'entries' => [
                    'date-to-placeholder' => 'بدون حد',
                    'allocation'          => 'عدد الأيام',
                    'allocation-value'    => ':days عدد الأيام',
                    'state'               => 'الحالة',
                ],
            ],
        ],
    ],
];
