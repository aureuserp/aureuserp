<?php

return [
    'title' => 'إدارة الحسابات الافتراضية',

    'setup' => [
        'action'       => 'إعداد المحاسبة لهذه الشركة',
        'notice'       => 'لم يتم إعداد المحاسبة لهذه الشركة بعد. استخدم "إعداد المحاسبة" أعلاه لإنشاء شجرة الحسابات ودفاتر اليومية والإعدادات الافتراضية.',
        'notification' => [
            'title' => 'تم إعداد المحاسبة',
            'body'  => 'تم إنشاء شجرة الحسابات ودفاتر اليومية والإعدادات الافتراضية لهذه الشركة.',
        ],
    ],

    'form' => [
        'exchange-difference-entries' => [
            'label' => 'قيود فروقات الصرف',

            'fields' => [
                'journal' => [
                    'label' => 'اليوميات',
                ],

                'gain' => [
                    'label' => 'أرباح',
                ],

                'loss' => [
                    'label' => 'خسائر',
                ],
            ],
        ],

        'bank-transfer-and-payments' => [
            'label' => 'التحويلات البنكية والمدفوعات',

            'fields' => [
                'bank-suspense-account' => [
                    'label' => 'حساب البنك المعلق',
                ],

                'transfer-account' => [
                    'label' => 'حساب التحويل',
                ],
            ],
        ],

        'product-accounts' => [
            'label' => 'حسابات المنتجات',

            'fields' => [
                'income-account' => [
                    'label' => 'حساب الإيرادات',
                ],

                'expense-account' => [
                    'label' => 'حساب المصروفات',
                ],
            ],
        ],
    ],
];
