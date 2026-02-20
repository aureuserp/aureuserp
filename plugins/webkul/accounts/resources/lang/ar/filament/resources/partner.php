<?php

return [
    'title' => 'الشركاء',

    'navigation' => [
        'title' => 'الشركاء',
    ],

    'form' => [
        'tabs' => [
            'sales-purchases' => [
                'fieldsets' => [
                    'sales' => [
                        'title' => 'المبيعات',

                        'fields' => [
                            'sales-person'   => 'مندوب المبيعات',
                            'payment-terms'  => 'شروط الدفع',
                            'payment-method' => 'طريقة الدفع',
                        ],
                    ],

                    'purchase' => [
                        'title' => 'المشتريات',

                        'fields' => [
                            'payment-terms'  => 'شروط الدفع',
                            'payment-method' => 'طريقة الدفع',
                        ],
                    ],

                    'fiscal-information' => [
                        'title' => 'المعلومات الضريبية',

                        'fields' => [
                            'fiscal-position'    => 'المركز الضريبي',
                        ],
                    ],
                ],
            ],

            'invoicing' => [
                'title'  => 'الفواتير',

                'fieldsets' => [
                    'customer-invoices' => [
                        'title' => 'فواتير العملاء',

                        'fields' => [
                            'invoice-sending-method'   => 'طريقة إرسال الفاتورة',
                            'invoice-edi-format-store' => 'صيغة الفاتورة الإلكترونية',
                            'peppol-eas'               => 'عنوان Peppol',
                            'endpoint'                 => 'نقطة النهاية',
                        ],
                    ],

                    'accounting-entries' => [
                        'title' => 'إدخالات المحاسبة',

                        'fields' => [
                            'account-receivable' => 'الحسابات المدينة',
                            'account-payable'    => 'الحسابات الدائنة',
                        ],
                    ],

                    'automation' => [
                        'title' => 'الأتمتة',

                        'fields' => [
                            'auto-post-bills' => 'نشر الفواتير تلقائياً',
                            'ignore-abnormal-invoice-amount' => 'تجاهل مبلغ الفاتورة غير العادي',
                            'ignore-abnormal-invoice-date' => 'تجاهل تاريخ الفاتورة غير العادي',
                        ],
                    ]
                ],
            ],

            'internal-notes' => [
                'title' => 'ملاحظات داخلية',
            ],
        ],
    ],

    'infolist' => [
        
        'tabs' => [
            'sales-purchases' => [
                'fieldsets' => [
                    'sales' => [
                        'title' => 'المبيعات',

                        'entries' => [
                            'sales-person'   => 'مندوب المبيعات',
                            'payment-terms'  => 'شروط الدفع',
                            'payment-method' => 'طريقة الدفع',
                        ],
                    ],

                    'purchase' => [
                        'title' => 'المشتريات',

                        'entries' => [
                            'payment-terms'  => 'شروط الدفع',
                            'payment-method' => 'طريقة الدفع',
                        ],
                    ],

                    'fiscal-information' => [
                        'title' => 'المعلومات الضريبية',

                        'entries' => [
                            'fiscal-position'    => 'المركز الضريبي',
                        ],
                    ],
                ],
            ],

            'invoicing' => [
                'title'  => 'الفواتير',

                'fieldsets' => [
                    'customer-invoices' => [
                        'title' => 'فواتير العملاء',

                        'entries' => [
                            'invoice-sending-method'   => 'طريقة إرسال الفاتورة',
                            'invoice-edi-format-store' => 'صيغة الفاتورة الإلكترونية',
                            'peppol-eas'               => 'عنوان Peppol',
                            'endpoint'                 => 'نقطة النهاية',
                        ],
                    ],

                    'accounting-entries' => [
                        'title' => 'إدخالات المحاسبة',

                        'entries' => [
                            'account-receivable' => 'الحسابات المدينة',
                            'account-payable'    => 'الحسابات الدائنة',
                        ],
                    ],

                    'automation' => [
                        'title' => 'الأتمتة',

                        'entries' => [
                            'auto-post-bills' => 'نشر الفواتير تلقائياً',
                            'ignore-abnormal-invoice-amount' => 'تجاهل مبلغ الفاتورة غير العادي',
                            'ignore-abnormal-invoice-date' => 'تجاهل تاريخ الفاتورة غير العادي',
                        ],
                    ]
                ],
            ],

            'internal-notes' => [
                'title' => 'ملاحظات داخلية',
            ],
        ],
    ],
];
