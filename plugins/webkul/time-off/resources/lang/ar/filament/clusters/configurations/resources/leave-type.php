<?php

return [
    'title'      => 'نوع الإجازة',
    'navigation' => [
        'title' => 'نوع الإجازة',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'معلومات عامة',
                'fields' => [
                    'name'                => 'العنوان',
                    'approval'            => 'الموافقة',
                    'requires-allocation' => 'يتطلب تخصيص',
                    'employee-requests'   => 'طلبات الموظفين',
                    'display-option'      => 'خيار العرض',
                ],
            ],
            'display-option' => [
                'title'  => 'خيار العرض',
                'fields' => [
                    'color' => 'اللون',
                ],
            ],
            'configuration' => [
                'title' => 'الإعدادات',

                'fields' => [
                    'notified-time-off-officers'          => 'مسؤولو الإجازات المُبلَّغون',
                    'take-time-off-in'                    => 'أخذ الإجازة بـ',
                    'public-holiday-included'             => 'تشمل العطل الرسمية',
                    'allow-to-attach-supporting-document' => 'السماح بإرفاق مستند داعم',
                    'show-on-dashboard'                   => 'العرض في لوحة التحكم',
                    'allow-negative-cap'                  => 'السماح بالرصيد السالب',
                    'kind-off-time'                       => 'نوع الوقت',
                    'max-negative-cap'                    => 'الحد الأقصى للرصيد السالب',
                    'kind-of-time'                        => 'نوع الإجازة',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'                      => 'الاسم',
            'company-name'              => 'الشركة',
            'color'                     => 'اللون',
            'notified-time-officers'    => 'مسؤولو الإجازات المُبلَّغون',
            'time-off-approval'         => 'موافقة الإجازة',
            'requires-allocation'       => 'يتطلب تخصيص',
            'allocation-approval'       => 'موافقة التخصيص',
            'employee-request'          => 'طلب الموظف',
        ],

        'filters' => [
            'name'                => 'الاسم',
            'company-name'        => 'الشركة',
            'time-off-approval'   => 'موافقة الإجازة',
            'requires-allocation' => 'يتطلب تخصيص',
            'time-type'           => 'نوع الوقت',
            'request-unit'        => 'وحدة الطلب',
            'created-by'          => 'أنشئ بواسطة',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'تم حذف نوع الإجازة',
                    'body'  => 'تم حذف نوع الإجازة بنجاح.',
                ],
            ],
            'restore' => [
                'notification' => [
                    'title' => 'تم استعادة نوع الإجازة',
                    'body'  => 'تم استعادة نوع الإجازة بنجاح.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'تم استعادة نوع الإجازة',
                    'body'  => 'تم استعادة نوع الإجازة بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف نوع الإجازة',
                    'body'  => 'تم حذف نوع الإجازة بنجاح.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'تم حذف نوع الإجازة نهائياً',
                        'body'  => 'تم حذف نوع الإجازة نهائياً بنجاح.',
                    ],
                    'error' => [
                        'title' => 'تعذر حذف نوع الإجازة',
                        'body'  => 'لا يمكن حذف نوع الإجازة لأنه قيد الاستخدام حالياً.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'   => 'معلومات عامة',
                'entries' => [
                    'name'                => 'العنوان',
                    'approval'            => 'الموافقة',
                    'requires-allocation' => 'يتطلب تخصيص',
                    'employee-requests'   => 'طلبات الموظفين',
                    'display-option'      => 'خيار العرض',
                ],
            ],
            'display-option' => [
                'title'   => 'خيار العرض',
                'entries' => [
                    'color' => 'اللون',
                ],
            ],
            'configuration' => [
                'title' => 'الإعدادات',

                'entries' => [
                    'notified-time-off-officers'          => 'مسؤولو الإجازات المُبلَّغون',
                    'take-time-off-in'                    => 'أخذ الإجازة بـ',
                    'public-holiday-included'             => 'تشمل العطل الرسمية',
                    'allow-to-attach-supporting-document' => 'السماح بإرفاق مستند داعم',
                    'show-on-dashboard'                   => 'العرض في لوحة التحكم',
                    'kind-off-time'                       => 'نوع الوقت',
                    'max-negative-cap'                    => 'الحد الأقصى للرصيد السالب',
                    'kind-of-time'                        => 'نوع الإجازة',
                ],
            ],
        ],
    ],
];
