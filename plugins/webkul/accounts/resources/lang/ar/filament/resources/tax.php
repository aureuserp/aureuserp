<?php

return [
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

            'repeater' => [
                'invoice-repartition-lines' => [
                    'label' => 'بنود تقسيم الفاتورة',
                ],

                'refund-repartition-lines' => [
                    'label' => 'بنود تقسيم الاسترداد',
                ],

                'fields' => [
                    'type'           => 'النوع',
                    'factor-percent' => 'النسبة المئوية للعامل',
                    'account'        => 'الحساب',
                ],
            ],

            'field-set' => [
                'advanced-options' => [
                    'title' => 'خيارات متقدمة',

                    'fields' => [
                        'invoice-label'       => 'تسمية الفاتورة',
                        'tax-group'           => 'مجموعة الضرائب',
                        'country'             => 'البلد',
                        'include-in-price'    => 'مضمّن في السعر',
                        'include-base-amount' => 'التأثير على أساس الضرائب اللاحقة',
                        'is-base-affected'    => 'الأساس يتأثر بالضرائب السابقة',
                    ],
                ],

                'fields' => [
                    'description' => 'الوصف',
                    'legal-notes' => 'الملاحظات القانونية',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'                   => 'الاسم',
            'amount-type'            => 'نوع المبلغ',
            'company'                => 'الشركة',
            'tax-group'              => 'مجموعة الضرائب',
            'country'                => 'البلد',
            'tax-type'               => 'نوع الضريبة',
            'tax-scope'              => 'نطاق الضريبة',
            'amount-type'            => 'نوع المبلغ',
            'invoice-label'          => 'تسمية الفاتورة',
            'tax-exigibility'        => 'استحقاق الضريبة',
            'price-include-override' => 'تجاوز تضمين السعر',
            'amount'                 => 'المبلغ',
            'status'                 => 'الحالة',
            'include-base-amount'    => 'تضمين المبلغ الأساسي',
            'is-base-affected'       => 'هل يتأثر الأساس',
        ],

        'groups' => [
            'name'         => 'الاسم',
            'company'      => 'الشركة',
            'tax-group'    => 'مجموعة الضرائب',
            'country'      => 'البلد',
            'created-by'   => 'أنشئ بواسطة',
            'type-tax-use' => 'نوع استخدام الضريبة',
            'tax-scope'    => 'نطاق الضريبة',
            'amount-type'  => 'نوع المبلغ',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'تم حذف الضريبة',
                        'body'  => 'تم حذف الضريبة بنجاح.',
                    ],

                    'error' => [
                        'title' => 'تعذر حذف الضريبة',
                        'body'  => 'لا يمكن حذف الضريبة لأنها قيد الاستخدام حالياً.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'تم حذف الضرائب',
                        'body'  => 'تم حذف الضرائب بنجاح.',
                    ],

                    'error' => [
                        'title' => 'تعذر حذف الضرائب',
                        'body'  => 'لا يمكن حذف الضرائب لأنها قيد الاستخدام حالياً.',
                    ],
                ],
            ],
        ],

        'pages' => [
            'create' => [
                'notifications' => [
                    'invalid-repartition-lines' => [
                        'title' => 'بنود تقسيم غير صالحة',
                    ],
                ],
            ],

            'edit' => [
                'notifications' => [
                    'invalid-repartition-lines' => [
                        'title' => 'بنود تقسيم غير صالحة',
                    ],
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
                        'country'             => 'البلد',
                        'include-in-price'    => 'تضمين في السعر',
                        'include-base-amount' => 'تضمين المبلغ الأساسي',
                        'is-base-affected'    => 'هل يتأثر الأساس',
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
