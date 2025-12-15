<?php

return [
    'title' => 'الحسابات',

    'navigation' => [
        'title' => 'الحسابات',
        'group' => 'المحاسبة',
    ],

    'global-search' => [
        'currency' => 'العملة',
        'name'     => 'الاسم',
    ],

    'form' => [
        'sections' => [
            'fields' => [
                'code'          => 'الرمز',
                'account-name'  => 'اسم الحساب',
                'accounting'    => 'المحاسبة',
                'account-type'  => 'نوع الحساب',
                'default-taxes' => 'الضرائب الافتراضية',
                'tags'          => 'الوسوم',
                'journals'      => 'اليوميات',
                'currency'      => 'العملة',
                'deprecated'    => 'مهمل',
                'reconcile'     => 'تسوية',
                'non-trade'     => 'غير تجاري',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'code'         => 'الرمز',
            'account-name' => 'اسم الحساب',
            'account-type' => 'نوع الحساب',
            'currency'     => 'العملة',
            'deprecated'   => 'مهمل',
            'reconcile'    => 'تسوية',
            'non-trade'    => 'غير تجاري',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'تم حذف الحساب',
                    'body'  => 'تم حذف الحساب بنجاح.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'تم حذف الحسابات',
                    'body'  => 'تم حذف الحسابات بنجاح.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'entries' => [
                'name'            => 'الاسم',
                'tax-type'        => 'نوع الضريبة',
                'tax-computation' => 'حساب الضريبة',
                'tax-scope'       => 'نطاق الضريبة',
                'status'          => 'الحالة',
                'amount'          => 'المبلغ',
            ],

            'field-set' => [
                'advanced-options' => [
                    'title' => 'خيارات متقدمة',

                    'entries' => [
                        'invoice-label'       => 'تسمية الفاتورة',
                        'tax-group'           => 'مجموعة الضرائب',
                        'country'             => 'الدولة',
                        'include-in-price'    => 'تضمين في السعر',
                        'include-base-amount' => 'تضمين المبلغ الأساسي',
                        'is-base-affected'    => 'هل الأساس متأثر',
                    ],
                ],

                'description-and-legal-notes' => [
                    'title'   => 'الوصف والملاحظات القانونية للفاتورة',
                    'entries' => [
                        'description' => 'الوصف',
                        'legal-notes' => 'الملاحظات القانونية',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'entries' => [
                'code'          => 'الرمز',
                'account-name'  => 'اسم الحساب',
                'accounting'    => 'المحاسبة',
                'account-type'  => 'نوع الحساب',
                'default-taxes' => 'الضرائب الافتراضية',
                'tags'          => 'الوسوم',
                'journals'      => 'اليوميات',
                'currency'      => 'العملة',
                'deprecated'    => 'مهمل',
                'reconcile'     => 'تسوية',
                'non-trade'     => 'غير تجاري',
            ],
        ],
    ],
];
