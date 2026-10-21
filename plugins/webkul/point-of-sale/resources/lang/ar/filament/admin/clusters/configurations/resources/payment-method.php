<?php

return [
    'navigation' => [
        'title' => 'طرق الدفع',
        'group' => 'نقطة البيع',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'عام',

                'fields' => [
                    'name'                             => 'الاسم',
                    'company'                          => 'الشركة',
                    'terminal-type'                    => 'التكامل',
                    'is-split-transaction'             => 'تحديد هوية العميل',
                    'is-split-transaction-helper-text' => 'ترحيل قيد محاسبي لكل دفعة على ذمم العميل المدينة.',
                    'is-active'                        => 'نشط',
                ],
            ],

            'accounting' => [
                'title'  => 'المحاسبة',

                'fields' => [
                    'journal'             => 'دفتر اليومية',
                    'journal-placeholder' => 'اتركه فارغاً لاستخدام حساب الذمم المدينة الخاص بالعميل',
                    'account-placeholder' => 'اتركه فارغاً لاستخدام الحساب الافتراضي من إعدادات الشركة',
                    'receivable-account'  => 'الحساب الوسيط',
                    'outstanding-account' => 'الحساب المعلّق',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'   => 'عام',

                'entries' => [
                    'name'                    => 'الاسم',
                    'type'                    => 'النوع',
                    'terminal-type'           => 'طرفية الدفع',
                    'journal-name'            => 'دفتر اليومية',
                    'receivableAccount-name'  => 'حساب الذمم المدينة',
                    'outstandingAccount-name' => 'الحساب المعلّق',
                    'is-cash-count'           => 'معدود في درج النقد',
                    'is-split-transaction'    => 'تقسيم المعاملات',
                    'is-active'               => 'نشط',
                    'company-name'            => 'الشركة',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'               => 'الاسم',
            'type'               => 'النوع',
            'journal'            => 'دفتر اليومية',
            'configs'            => 'POS',
            'terminal-type'      => 'التكامل',
            'receivable-account' => 'الحساب الوسيط',
            'is-cash-count'      => 'درج النقد',
            'is-active'          => 'نشط',
            'company'            => 'الشركة',
        ],

        'groups' => [
            'type'    => 'النوع',
            'company' => 'الشركة',
        ],

        'filters' => [
            'type' => 'النوع',
        ],

        'record-actions' => [
            'restore' => [
                'notification' => [
                    'success' => [
                        'title' => 'تمت استعادة طريقة الدفع',
                        'body'  => 'تمت استعادة طريقة الدفع.',
                    ],
                ],
            ],

            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'تم حذف طريقة الدفع',
                        'body'  => 'تم حذف طريقة الدفع.',
                    ],
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'تم حذف طريقة الدفع نهائياً',
                        'body'  => 'تم حذف طريقة الدفع نهائياً.',
                    ],

                    'error' => [
                        'title' => 'تعذر حذف طريقة الدفع',
                        'body'  => 'لا يمكن حذف طريقة الدفع نهائياً لأنها تحتوي على مدفوعات. قم بأرشفتها بدلاً من ذلك.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'تم حذف طرق الدفع نهائياً',
                        'body'  => 'تم حذف طرق الدفع المحددة نهائياً.',
                    ],

                    'error' => [
                        'title' => 'تعذر حذف طرق الدفع',
                        'body'  => 'لا يمكن حذف طريقة دفع واحدة أو أكثر نهائياً لأنها تحتوي على مدفوعات. قم بأرشفتها بدلاً من ذلك.',
                    ],
                ],
            ],
        ],
    ],

    'in-open-session' => [
        'title' => 'طريقة الدفع قيد الاستخدام',
    ],
];
