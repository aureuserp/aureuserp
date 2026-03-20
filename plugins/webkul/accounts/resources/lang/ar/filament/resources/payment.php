<?php

return [
    'title' => 'دفعة',

    'navigation' => [
        'title' => 'المدفوعات',
        'group' => 'الفواتير',
    ],

    'global-search' => [
        'partner' => 'الشريك',
        'amount'  => 'المبلغ',
        'date'    => 'التاريخ',
    ],

    'form' => [
        'sections' => [
            'fields' => [
                'payment-type'          => 'نوع الدفع',
                'memo'                  => 'مذكرة',
                'date'                  => 'التاريخ',
                'amount'                => 'المبلغ',
                'currency'              => 'العملة',
                'payment-method'        => 'طريقة الدفع',
                'customer'              => 'العميل',
                'vendor'                => 'المورد',
                'journal'               => 'اليومية',
                'customer-bank-account' => 'الحساب البنكي للعميل',
                'vendor-bank-account'   => 'الحساب البنكي للمورد',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'            => 'الاسم',
            'date'            => 'التاريخ',
            'journal'         => 'اليومية',
            'payment-method'  => 'طريقة الدفع',
            'partner'         => 'الشريك',
            'amount-currency' => 'المبلغ (العملة)',
            'amount'          => 'المبلغ',
            'state'           => 'الحالة',
            'company'         => 'الشركة',
            'currency'        => 'العملة',
            'created-by'      => 'أنشئ بواسطة',
        ],

        'groups' => [
            'name'                 => 'الاسم',
            'company'              => 'الشركة',
            'journal'              => 'اليومية',
            'partner'              => 'الشريك',
            'payment-method-line'  => 'بند طريقة الدفع',
            'payment-method'       => 'طريقة الدفع',
            'partner-bank-account' => 'الحساب البنكي للشريك',
            'created-at'           => 'تاريخ الإنشاء',
            'updated-at'           => 'تاريخ التحديث',
        ],

        'filters' => [
            'company'               => 'الشركة',
            'journal'               => 'اليومية',
            'customer-bank-account' => 'الحساب البنكي للعميل',
            'payment-method'        => 'طريقة الدفع',
            'currency'              => 'العملة',
            'partner'               => 'الشريك',
            'payment-method-line'   => 'بند طريقة الدفع',
            'created-at'            => 'تاريخ الإنشاء',
            'updated-at'            => 'تاريخ التحديث',
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

        'toolbar-actions' => [
            'export' => [
                'label' => 'تصدير',
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'payment-information' => [
                'title'   => 'معلومات الدفع',
                'entries' => [
                    'state'                 => 'الحالة',
                    'vendor'                => 'المورد',
                    'customer'              => 'العميل',
                    'payment-type'          => 'نوع الدفع',
                    'journal'               => 'اليومية',
                    'customer-bank-account' => 'الحساب البنكي للعميل',
                    'vendor-bank-account'   => 'الحساب البنكي للمورد',
                    'amount'                => 'المبلغ',
                    'payment-method'        => 'طريقة الدفع',
                    'date'                  => 'التاريخ',
                    'memo'                  => 'مذكرة',
                ],
            ],
        ],
    ],
];
