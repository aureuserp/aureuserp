<?php

return [
    'title' => 'فاتورة مورد',

    'navigation' => [
        'title' => 'فواتير الموردين',
        'group' => 'الفواتير',
    ],

    'global-search' => [
        'vendor'   => 'المورد',
        'date'     => 'التاريخ',
        'due-date' => 'تاريخ الاستحقاق',
    ],

    'form' => [
        'section' => [
            'general' => [
                'title'  => 'عام',
                'fields' => [
                    'vendor-bill'       => 'فاتورة المورد',
                    'vendor'            => 'المورد',
                    'bill-date'         => 'تاريخ الفاتورة',
                    'bill-reference'    => 'مرجع الفاتورة',
                    'accounting-date'   => 'تاريخ المحاسبة',
                    'payment-reference' => 'مرجع الدفع',
                    'recipient-bank'    => 'بنك المستلم',
                    'due-date'          => 'تاريخ الاستحقاق',
                    'payment-term'      => 'شروط الدفع',
                    'journal'           => 'دفتر اليومية',
                    'currency'          => 'العملة',
                ],
            ],
        ],

        'tabs' => [
            'invoice-lines' => [
                'title' => 'بنود الفاتورة',

                'repeater' => [
                    'products' => [
                        'title'       => 'المنتجات',
                        'add-product' => 'إضافة منتج',

                        'columns' => [
                            'product'             => 'المنتج',
                            'quantity'            => 'الكمية',
                            'unit'                => 'الوحدة',
                            'taxes'               => 'الضرائب',
                            'discount-percentage' => 'نسبة الخصم',
                            'unit-price'          => 'سعر الوحدة',
                            'sub-total'           => 'المجموع الفرعي',
                        ],

                        'fields' => [
                            'product'             => 'المنتج',
                            'quantity'            => 'الكمية',
                            'unit'                => 'الوحدة',
                            'taxes'               => 'الضرائب',
                            'discount-percentage' => 'نسبة الخصم',
                            'unit-price'          => 'سعر الوحدة',
                            'sub-total'           => 'المجموع الفرعي',
                        ],
                    ],
                ],
            ],

            'other-information' => [
                'title'    => 'معلومات أخرى',

                'fieldset' => [
                    'accounting' => [
                        'title' => 'المحاسبة',

                        'fields' => [
                            'company'                 => 'الشركة',
                            'incoterm'                => 'شروط التجارة',
                            'incoterm-location'       => 'موقع شروط التجارة',
                            'payment-method'          => 'طريقة الدفع',
                            'fiscal-position'         => 'الموقف المالي',
                            'fiscal-position-tooltip' => 'تُستخدم المواقف المالية لتعديل الضرائب والحسابات بناءً على موقع العميل.',
                            'cash-rounding'           => 'طريقة التقريب النقدي',
                            'cash-rounding-tooltip'   => 'تحدد أصغر وحدة نقدية قابلة للدفع من العملة.',
                            'auto-post'               => 'ترحيل تلقائي',
                            'checked'                 => 'تم التحقق',
                        ],
                    ],
                ],
            ],

            'term-and-conditions' => [
                'title' => 'الشروط والأحكام',
            ],
        ],
    ],

    'table' => [
        'total'   => 'الإجمالي',
        'columns' => [
            'number'           => 'الرقم',
            'state'            => 'الحالة',
            'customer'         => 'العميل',
            'bill-date'        => 'تاريخ الفاتورة',
            'checked'          => 'تم التحقق',
            'accounting-date'  => 'تاريخ المحاسبة',
            'due-date'         => 'تاريخ الاستحقاق',
            'source-document'  => 'المستند المصدر',
            'reference'        => 'المرجع',
            'sales-person'     => 'مندوب المبيعات',
            'tax-excluded'     => 'بدون ضريبة',
            'tax'              => 'الضريبة',
            'total'            => 'الإجمالي',
            'amount-due'       => 'المبلغ المستحق',
            'bill-currency'    => 'عملة الفاتورة',
        ],

        'summarizers' => [
            'total' => 'الإجمالي',
        ],

        'groups' => [
            'name'                         => 'الاسم',
            'bill-partner-display-name'    => 'اسم شريك الفاتورة',
            'bill-date'                    => 'تاريخ الفاتورة',
            'checked'                      => 'تم التحقق',
            'date'                         => 'التاريخ',
            'bill-due-date'                => 'تاريخ استحقاق الفاتورة',
            'bill-origin'                  => 'مصدر الفاتورة',
            'sales-person'                 => 'مندوب المبيعات',
            'currency'                     => 'العملة',
            'created-at'                   => 'تاريخ الإنشاء',
            'updated-at'                   => 'تاريخ التحديث',
        ],

        'filters' => [
            'number'                    => 'الرقم',
            'bill-partner-display-name' => 'اسم شريك الفاتورة',
            'bill-date'                 => 'تاريخ الفاتورة',
            'bill-due-date'             => 'تاريخ استحقاق الفاتورة',
            'bill-origin'               => 'مصدر الفاتورة',
            'reference'                 => 'المرجع',
            'payment-reference'         => 'مرجع الدفع',
            'narration'                 => 'البيان',
            'partner'                   => 'الشريك',
            'journal'                   => 'دفتر اليومية',
            'fiscal-position'           => 'الموقف المالي',
            'currency'                  => 'العملة',
            'company'                   => 'الشركة',
            'date'                      => 'تاريخ المحاسبة',
            'delivery-date'             => 'تاريخ التسليم',
            'amount-untaxed'            => 'المبلغ بدون ضريبة',
            'amount-tax'                => 'مبلغ الضريبة',
            'amount-total'              => 'المبلغ الإجمالي',
            'amount-residual'           => 'المبلغ المستحق',
            'checked'                   => 'تم التحقق',
            'posted-before'             => 'مُرحّل قبل',
            'is-move-sent'              => 'تم الإرسال',
            'created-at'                => 'تاريخ الإنشاء',
            'updated-at'                => 'تاريخ التحديث',
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
        'section' => [
            'general' => [
                'title'   => 'عام',

                'entries' => [
                    'vendor-invoice'    => 'فاتورة المورد',
                    'vendor'            => 'المورد',
                    'bill-date'         => 'تاريخ الفاتورة',
                    'bill-reference'    => 'مرجع الفاتورة',
                    'accounting-date'   => 'تاريخ المحاسبة',
                    'payment-reference' => 'مرجع الدفع',
                    'recipient-bank'    => 'بنك المستلم',
                    'due-date'          => 'تاريخ الاستحقاق',
                    'payment-term'      => 'شروط الدفع',
                    'journal'           => 'دفتر اليومية',
                    'currency'          => 'العملة',
                ],
            ],
        ],

        'tabs' => [
            'invoice-lines' => [
                'title' => 'بنود الفاتورة',

                'repeater' => [
                    'products' => [
                        'title'       => 'المنتجات',
                        'add-product' => 'إضافة منتج',

                        'entries' => [
                            'product'             => 'المنتج',
                            'quantity'            => 'الكمية',
                            'unit'                => 'الوحدة',
                            'taxes'               => 'الضرائب',
                            'discount-percentage' => 'نسبة الخصم',
                            'unit-price'          => 'سعر الوحدة',
                            'sub-total'           => 'المجموع الفرعي',
                        ],
                    ],
                ],
            ],

            'other-information' => [
                'title'    => 'معلومات أخرى',
                'fieldset' => [
                    'accounting' => [
                        'title' => 'المحاسبة',

                        'entries' => [
                            'company'           => 'الشركة',
                            'incoterm'          => 'شروط التجارة',
                            'incoterm-location' => 'موقع شروط التجارة',
                            'payment-method'    => 'طريقة الدفع',
                            'checked'           => 'تم التحقق',
                            'fiscal-position'   => 'الموقف المالي',
                            'cash-rounding'     => 'طريقة التقريب النقدي',
                            'checked'           => 'تم التحقق',
                        ],
                    ],
                ],
            ],

            'term-and-conditions' => [
                'title' => 'الشروط والأحكام',
            ],

            'journal-items' => [
                'title' => 'بنود اليومية',

                'repeater' => [
                    'entries' => [
                        'account'  => 'الحساب',
                        'partner'  => 'الشريك',
                        'label'    => 'التسمية',
                        'due-date' => 'تاريخ الاستحقاق',
                        'currency' => 'العملة',
                        'taxes'    => 'الضرائب',
                        'debit'    => 'مدين',
                        'credit'   => 'دائن',
                    ],
                ],
            ],
        ],
    ],
];
