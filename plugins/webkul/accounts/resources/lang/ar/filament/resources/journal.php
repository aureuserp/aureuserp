<?php

return [
    'form' => [
        'tabs' => [
            'journal-entries' => [
                'title' => 'قيود اليومية',

                'field-set' => [
                    'accounting-information' => [
                        'title'  => 'معلومات المحاسبة',
                        'fields' => [
                            'dedicated-credit-note-sequence' => 'تسلسل إشعار دائن مخصص',
                            'dedicated-payment-sequence'     => 'تسلسل دفع مخصص',
                            'sort-code-placeholder'          => 'أدخل رمز اليومية',
                            'sort-code'                      => 'الترتيب',
                            'currency'                       => 'العملة',
                            'color'                          => 'اللون',
                            'default-account'                => 'الحساب الافتراضي',
                            'profit-account'                 => 'حساب الأرباح',
                            'loss-account'                   => 'حساب الخسائر',
                            'suspense-account'               => 'حساب المعلقات',
                            'bank-account'                   => 'الحساب البنكي',
                        ],
                    ],

                    'bank-account-number' => [
                        'title' => 'رقم الحساب البنكي',
                    ],
                ],
            ],

            'incoming-payments' => [
                'title'            => 'المدفوعات الواردة',
                'add-action-label' => 'إضافة سطر',

                'fields' => [
                    'payment-method'             => 'طريقة الدفع',
                    'display-name'               => 'الاسم المعروض',
                    'account-number'             => 'حسابات الإيصالات المعلقة',
                    'relation-notes'             => 'ملاحظات العلاقة',
                    'relation-notes-placeholder' => 'أدخل تفاصيل العلاقة',
                ],
            ],

            'outgoing-payments' => [
                'title'            => 'المدفوعات الصادرة',
                'add-action-label' => 'إضافة سطر',

                'fields' => [
                    'payment-method'             => 'طريقة الدفع',
                    'display-name'               => 'الاسم المعروض',
                    'account-number'             => 'حسابات المدفوعات المعلقة',
                    'relation-notes'             => 'ملاحظات العلاقة',
                    'relation-notes-placeholder' => 'أدخل تفاصيل العلاقة',
                ],
            ],

            'advanced-settings' => [
                'title'  => 'إعدادات متقدمة',

                'fields' => [
                    'allowed-accounts'       => 'الحسابات المسموح بها',
                    'control-access'         => 'التحكم في الوصول',
                    'payment-communication'  => 'اتصال الدفع',
                    'auto-check-on-post'     => 'فحص تلقائي عند الترحيل',
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
                    'success' => [
                        'title' => 'تم حذف دفتر اليومية',
                        'body'  => 'تم حذف دفتر اليومية بنجاح.',
                    ],

                    'error' => [
                        'title' => 'فشل حذف دفتر اليومية',
                        'body'  => 'لا يمكن حذف دفتر اليومية لأنه قيد الاستخدام حالياً.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'تم حذف دفتر اليومية',
                        'body'  => 'تم حذف دفتر اليومية بنجاح.',
                    ],

                    'error' => [
                        'title' => 'فشل حذف دفاتر اليومية',
                        'body'  => 'لا يمكن حذف دفاتر اليومية لأنها قيد الاستخدام حالياً.',
                    ],
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
                            'dedicated-credit-note-sequence' => 'تسلسل إشعار دائن مخصص',
                            'dedicated-payment-sequence'     => 'تسلسل دفع مخصص',
                            'sort-code-placeholder'          => 'أدخل رمز اليومية',
                            'sort-code'                      => 'الترتيب',
                            'currency'                       => 'العملة',
                            'color'                          => 'اللون',
                            'default-account'                => 'الحساب الافتراضي',
                            'profit-account'                 => 'حساب الأرباح',
                            'loss-account'                   => 'حساب الخسائر',
                            'suspense-account'               => 'حساب المعلقات',
                        ],
                    ],

                    'bank-account-number' => [
                        'title' => 'رقم الحساب البنكي',

                        'entries' => [
                            'account-number' => 'رقم الحساب',
                        ],
                    ],
                ],
            ],

            'incoming-payments' => [
                'title' => 'المدفوعات الواردة',

                'entries' => [
                    'payment-method'             => 'طريقة الدفع',
                    'display-name'               => 'الاسم المعروض',
                    'account-number'             => 'حسابات الإيصالات المعلقة',
                    'relation-notes'             => 'ملاحظات العلاقة',
                    'relation-notes-placeholder' => 'أدخل تفاصيل العلاقة',
                ],
            ],

            'outgoing-payments' => [
                'title' => 'المدفوعات الصادرة',

                'entries' => [
                    'payment-method'             => 'طريقة الدفع',
                    'display-name'               => 'الاسم المعروض',
                    'account-number'             => 'حسابات المدفوعات المعلقة',
                    'relation-notes'             => 'ملاحظات العلاقة',
                    'relation-notes-placeholder' => 'أدخل تفاصيل العلاقة',
                ],
            ],

            'advanced-settings' => [
                'title'   => 'إعدادات متقدمة',

                'allowed-accounts' => [
                    'title' => 'الحسابات المسموح بها',

                    'entries' => [
                        'allowed-accounts'       => 'الحسابات المسموح بها',
                        'control-access'         => 'التحكم في الوصول',
                        'auto-check-on-post'     => 'فحص تلقائي عند الترحيل',
                    ],
                ],

                'payment-communication'  => [
                    'title' => 'اتصال الدفع',

                    'entries' => [
                        'communication-type'     => 'نوع الاتصال',
                        'communication-standard' => 'معيار الاتصال',
                    ],
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
