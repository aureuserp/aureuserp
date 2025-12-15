<?php

return [
    'title' => 'دفعة',

    'navigation' => [
        'title' => 'المدفوعات',
        'group' => 'الفواتير',
    ],

    'form' => [
        'sections' => [
            'fields' => [
                'payment-type'          => 'نوع الدفع',
                'memo'                  => 'مذكرة',
                'date'                  => 'التاريخ',
                'amount'                => 'المبلغ',
                'payment-method'        => 'طريقة الدفع',
                'customer'              => 'العميل',
                'journal'               => 'اليومية',
                'customer-bank-account' => 'الحساب البنكي للعميل',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'                             => 'الاسم',
            'company'                          => 'الشركة',
            'bank-account-holder'              => 'صاحب الحساب البنكي',
            'paired-internal-transfer-payment' => 'دفعة التحويل الداخلي المقترنة',
            'payment-method-line'              => 'بند طريقة الدفع',
            'payment-method'                   => 'طريقة الدفع',
            'currency'                         => 'العملة',
            'partner'                          => 'الشريك',
            'outstanding-amount'               => 'المبلغ المستحق',
            'destination-account'              => 'حساب الوجهة',
            'created-by'                       => 'أنشئ بواسطة',
            'payment-transaction'              => 'معاملة الدفع',
        ],

        'groups' => [
            'name'                             => 'الاسم',
            'company'                          => 'الشركة',
            'partner'                          => 'الشريك',
            'payment-method-line'              => 'بند طريقة الدفع',
            'payment-method'                   => 'طريقة الدفع',
            'partner-bank-account'             => 'الحساب البنكي للشريك',
            'paired-internal-transfer-payment' => 'دفعة التحويل الداخلي المقترنة',
            'created-at'                       => 'تاريخ الإنشاء',
            'updated-at'                       => 'تاريخ التحديث',
        ],

        'filters' => [
            'company'                          => 'الشركة',
            'customer-bank-account'            => 'الحساب البنكي للعميل',
            'paired-internal-transfer-payment' => 'دفعة التحويل الداخلي المقترنة',
            'payment-method'                   => 'طريقة الدفع',
            'currency'                         => 'العملة',
            'partner'                          => 'الشريك',
            'payment-method-line'              => 'بند طريقة الدفع',
            'created-at'                       => 'تاريخ الإنشاء',
            'updated-at'                       => 'تاريخ التحديث',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'تم حذف الدفعة',
                    'body'  => 'تم حذف الدفعة بنجاح.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'تم حذف الدفعات',
                    'body'  => 'تم حذف الدفعات بنجاح.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'payment-information' => [
                'title'   => 'معلومات الدفع',
                'entries' => [
                    'state'                 => 'الحالة',
                    'payment-type'          => 'نوع الدفع',
                    'journal'               => 'اليومية',
                    'customer-bank-account' => 'الحساب البنكي للعميل',
                    'customer'              => 'العميل',
                ],
            ],

            'payment-details' => [
                'title'   => 'تفاصيل الدفع',
                'entries' => [
                    'amount' => 'المبلغ',
                    'date'   => 'التاريخ',
                    'memo'   => 'مذكرة',
                ],
            ],

            'payment-method' => [
                'title'   => 'طريقة الدفع',
                'entries' => [
                    'payment-method' => 'طريقة الدفع',
                ],
            ],
        ],
    ],

];
