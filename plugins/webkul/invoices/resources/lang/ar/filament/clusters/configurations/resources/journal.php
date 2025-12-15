<?php

return [
    'title' => 'اليومية',

    'navigation' => [
        'title' => 'اليومية',
        'group' => 'المحاسبة',
    ],

    'global-search' => [
        'name' => 'الاسم',
        'code' => 'الرمز',
    ],

    'form' => [
        'tabs' => [
            'journal-entries' => [
                'title' => 'قيود اليومية',

                'field-set' => [
                    'accounting-information' => [
                        'title'  => 'معلومات المحاسبة',
                        'fields' => [
                            'dedicated-credit-note-sequence' => 'تسلسل إشعار الائتمان المخصص',
                            'dedicated-payment-sequence'     => 'تسلسل الدفع المخصص',
                            'sort-code-placeholder'          => 'أدخل رمز اليومية',
                            'sort-code'                      => 'الترتيب',
                            'currency'                       => 'العملة',
                            'color'                          => 'اللون',
                        ],
                    ],
                    'bank-account-number' => [
                        'title' => 'رقم الحساب البنكي',
                    ],
                ],
            ],
            'incoming-payments' => [
                'title' => 'المدفوعات الواردة',

                'fields' => [
                    'relation-notes'             => 'ملاحظات العلاقة',
                    'relation-notes-placeholder' => 'أدخل أي تفاصيل للعلاقة',
                ],
            ],
            'outgoing-payments' => [
                'title' => 'المدفوعات الصادرة',

                'fields' => [
                    'relation-notes'             => 'ملاحظات العلاقة',
                    'relation-notes-placeholder' => 'أدخل أي تفاصيل للعلاقة',
                ],
            ],
            'advanced-settings' => [
                'title'  => 'الإعدادات المتقدمة',
                'fields' => [
                    'allowed-accounts'       => 'الحسابات المسموحة',
                    'control-access'         => 'التحكم في الوصول',
                    'payment-communication'  => 'اتصال الدفع',
                    'auto-check-on-post'     => 'التحقق التلقائي عند الترحيل',
                    'communication-type'     => 'نوع الاتصال',
                    'communication-standard' => 'معيار الاتصال',
                ],
            ],
        ],

        'general' => [
            'title' => 'معلومات عامة',

            'fields' => [
                'name'    => 'الاسم',
                'type'    => 'النوع',
                'company' => 'الشركة',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'الاسم',
            'type'       => 'النوع',
            'code'       => 'الرمز',
            'currency'   => 'العملة',
            'created-by' => 'أنشئ بواسطة',
            'status'     => 'الحالة',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'تم حذف اليومية',
                    'body'  => 'تم حذف اليومية بنجاح.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'تم حذف اليومية',
                    'body'  => 'تم حذف اليومية بنجاح.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'tabs' => [
            'journal-entries' => [
                'title' => 'قيود اليومية',

                'field-set' => [
                    'accounting-information' => [
                        'title'   => 'معلومات المحاسبة',
                        'entries' => [
                            'dedicated-credit-note-sequence' => 'تسلسل إشعار الائتمان المخصص',
                            'dedicated-payment-sequence'     => 'تسلسل الدفع المخصص',
                            'sort-code-placeholder'          => 'أدخل رمز اليومية',
                            'sort-code'                      => 'الترتيب',
                            'currency'                       => 'العملة',
                            'color'                          => 'اللون',
                        ],
                    ],
                    'bank-account-number' => [
                        'title' => 'رقم الحساب البنكي',
                    ],
                ],
            ],
            'incoming-payments' => [
                'title' => 'المدفوعات الواردة',

                'entries' => [
                    'relation-notes'             => 'ملاحظات العلاقة',
                    'relation-notes-placeholder' => 'أدخل أي تفاصيل للعلاقة',
                ],
            ],
            'outgoing-payments' => [
                'title' => 'المدفوعات الصادرة',

                'entries' => [
                    'relation-notes'             => 'ملاحظات العلاقة',
                    'relation-notes-placeholder' => 'أدخل أي تفاصيل للعلاقة',
                ],
            ],
            'advanced-settings' => [
                'title'   => 'الإعدادات المتقدمة',
                'entries' => [
                    'allowed-accounts'       => 'الحسابات المسموحة',
                    'control-access'         => 'التحكم في الوصول',
                    'payment-communication'  => 'اتصال الدفع',
                    'auto-check-on-post'     => 'التحقق التلقائي عند الترحيل',
                    'communication-type'     => 'نوع الاتصال',
                    'communication-standard' => 'معيار الاتصال',
                ],
            ],
        ],

        'general' => [
            'title' => 'معلومات عامة',

            'entries' => [
                'name'    => 'الاسم',
                'type'    => 'النوع',
                'company' => 'الشركة',
            ],
        ],
    ],

];
