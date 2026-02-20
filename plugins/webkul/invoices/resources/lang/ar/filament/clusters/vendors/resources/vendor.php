<?php

return [
    'title' => 'الموردون',

    'navigation' => [
        'title' => 'الموردون',
    ],

    'form' => [
        'fields' => [
            'sales-person'       => 'مندوب المبيعات',
            'payment-terms'      => 'شروط الدفع',
            'payment-method'     => 'طريقة الدفع',
            'fiscal-position'    => 'الموقف الضريبي',
            'purchase'           => 'الشراء',
            'fiscal-information' => 'المعلومات الضريبية',
        ],
        'tabs' => [
            'invoicing' => [
                'title'  => 'الفوترة',
                'fields' => [
                    'customer-invoices'              => 'فواتير العملاء',
                    'invoice-sending-method'         => 'طريقة إرسال الفاتورة',
                    'invoice-edi-format-store'       => 'تنسيق الفاتورة الإلكترونية',
                    'peppol-eas'                     => 'عنوان Peppol',
                    'endpoint'                       => 'نقطة النهاية',
                    'auto-post-bills'                => 'نشر الفواتير تلقائياً',
                    'automation'                     => 'الأتمتة',
                    'ignore-abnormal-invoice-amount' => 'تجاهل مبلغ الفاتورة غير العادي',
                    'ignore-abnormal-invoice-date'   => 'تجاهل تاريخ الفاتورة غير العادي',
                ],
            ],
            'internal-notes' => [
                'title' => 'ملاحظات داخلية',
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'sales-person'       => 'مندوب المبيعات',
            'payment-terms'      => 'شروط الدفع',
            'payment-method'     => 'طريقة الدفع',
            'fiscal-position'    => 'الموقف الضريبي',
            'purchase'           => 'الشراء',
            'fiscal-information' => 'المعلومات الضريبية',
        ],
        'tabs' => [
            'invoicing' => [
                'title'   => 'الفوترة',
                'entries' => [
                    'customer-invoices'              => 'فواتير العملاء',
                    'invoice-sending-method'         => 'طريقة إرسال الفاتورة',
                    'invoice-edi-format-store'       => 'تنسيق الفاتورة الإلكترونية',
                    'peppol-eas'                     => 'عنوان Peppol',
                    'endpoint'                       => 'نقطة النهاية',
                    'auto-post-bills'                => 'نشر الفواتير تلقائياً',
                    'automation'                     => 'الأتمتة',
                    'ignore-abnormal-invoice-amount' => 'تجاهل مبلغ الفاتورة غير العادي',
                    'ignore-abnormal-invoice-date'   => 'تجاهل تاريخ الفاتورة غير العادي',
                ],
            ],
            'internal-notes' => [
                'title' => 'ملاحظات داخلية',
            ],
        ],
    ],
];
