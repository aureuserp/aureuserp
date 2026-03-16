<?php

return [
    'title' => 'الضرائب',

    'navigation' => [
        'title' => 'الضرائب',
        'group' => 'المحاسبة',
    ],

    'global-search' => [
        'company'     => 'الشركة',
        'amount-type' => 'نوع المبلغ',
        'name'        => 'الاسم',
    ],

    'form' => [
        'sections' => [
            'fields' => [
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

                    'fields' => [
                        'invoice-label'       => 'تسمية الفاتورة',
                        'tax-group'           => 'مجموعة الضرائب',
                        'country'             => 'الدولة',
                        'include-in-price'    => 'تضمين في السعر',
                        'include-base-amount' => 'تضمين المبلغ الأساسي',
                        'is-base-affected'    => 'هل الأساس متأثر',
                    ],
                ],

                'description-and-legal-notes' => [
                    'title'  => 'الوصف والملاحظات القانونية للفاتورة',
                    'fields' => [
                        'description' => 'الوصف',
                        'legal-notes' => 'الملاحظات القانونية',
                    ],
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'                    => 'الاسم',
            'amount-type'             => 'نوع المبلغ',
            'company'                 => 'الشركة',
            'tax-group'               => 'مجموعة الضرائب',
            'country'                 => 'الدولة',
            'type-tax-use'            => 'نوع استخدام الضريبة',
            'tax-scope'               => 'نطاق الضريبة',
            'amount-type'             => 'نوع المبلغ',
            'invoice-label'           => 'تسمية الفاتورة',
            'tax-exigibility'         => 'استحقاق الضريبة',
            'price-include-override'  => 'تجاوز تضمين السعر',
            'amount'                  => 'المبلغ',
            'status'                  => 'الحالة',
            'include-base-amount'     => 'تضمين المبلغ الأساسي',
            'is-base-affected'        => 'هل الأساس متأثر',
        ],

        'groups' => [
            'name'         => 'الاسم',
            'company'      => 'الشركة',
            'tax-group'    => 'مجموعة الضرائب',
            'country'      => 'الدولة',
            'created-by'   => 'أنشئ بواسطة',
            'type-tax-use' => 'نوع استخدام الضريبة',
            'tax-scope'    => 'نطاق الضريبة',
            'amount-type'  => 'نوع المبلغ',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'تم حذف الضريبة',
                    'body'  => 'تم حذف الضريبة بنجاح.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'تم حذف الضرائب',
                    'body'  => 'تم حذف الضرائب بنجاح.',
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

];
