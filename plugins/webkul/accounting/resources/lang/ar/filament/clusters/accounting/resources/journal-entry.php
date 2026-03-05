<?php

return [
    'title' => 'القيود اليومية',

    'navigation' => [
        'title' => 'القيود اليومية',
    ],

    'record-sub-navigation' => [
        'payment' => 'الدفعة',
    ],

    'global-search' => [
        'number'   => 'الرقم',
        'partner'  => 'الشريك',
        'date'     => 'تاريخ الفاتورة',
        'due-date' => 'تاريخ استحقاق الفاتورة',
    ],

    'form' => [
        'section' => [
            'general' => [
                'title'  => 'عام',

                'fields' => [
                    'reference'       => 'المرجع',
                    'accounting-date' => 'تاريخ المحاسبة',
                    'journal'         => 'اليومية',
                ],
            ],
        ],

        'tabs' => [
            'lines' => [
                'title' => 'عناصر اليومية',

                'repeater' => [
                    'title'    => 'العناصر',
                    'add-item' => 'إضافة عنصر',

                    'columns' => [
                        'account'                  => 'الحساب',
                        'partner'                  => 'الشريك',
                        'label'                    => 'التسمية',
                        'amount-currency'          => 'المبلغ (العملة)',
                        'currency'                 => 'العملة',
                        'taxes'                    => 'الضرائب',
                        'debit'                    => 'مدين',
                        'credit'                   => 'دائن',
                        'discount-amount-currency' => 'مبلغ الخصم (العملة)',
                    ],

                    'fields' => [
                        'account'                  => 'الحساب',
                        'partner'                  => 'الشريك',
                        'label'                    => 'التسمية',
                        'amount-currency'          => 'المبلغ (العملة)',
                        'currency'                 => 'العملة',
                        'taxes'                    => 'الضرائب',
                        'debit'                    => 'مدين',
                        'credit'                   => 'دائن',
                        'discount-amount-currency' => 'مبلغ الخصم (العملة)',
                    ],
                ],
            ],

            'other-information' => [
                'title' => 'معلومات أخرى',

                'fields' => [
                    'checked'         => 'تم التحقق',
                    'company'         => 'الشركة',
                    'fiscal-position' => 'الموقف الضريبي',
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
            'invoice-date' => 'تاريخ الفاتورة',
            'date'         => 'التاريخ',
            'number'       => 'الرقم',
            'partner'      => 'الشريك',
            'reference'    => 'المرجع',
            'journal'      => 'اليومية',
            'company'      => 'الشركة',
            'total'        => 'الإجمالي',
            'state'        => 'الحالة',
            'checked'      => 'تم التحقق',
        ],

        'summarizers' => [
            'total' => 'الإجمالي',
        ],

        'groups' => [
            'partner'        => 'الشريك',
            'journal'        => 'اليومية',
            'state'          => 'الحالة',
            'payment-method' => 'طريقة الدفع',
            'date'           => 'التاريخ',
            'invoice-date'   => 'تاريخ الفاتورة',
            'company'        => 'الشركة',
        ],

        'filters' => [
            'number'                        => 'الرقم',
            'invoice-partner-display-name'  => 'اسم شريك الفاتورة المعروض',
            'invoice-date'                  => 'تاريخ الفاتورة',
            'invoice-due-date'              => 'تاريخ استحقاق الفاتورة',
            'invoice-origin'                => 'مصدر الفاتورة',
            'reference'                     => 'المرجع',
            'created-at'                    => 'تاريخ الإنشاء',
            'updated-at'                    => 'تاريخ التحديث',
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
                    'number'          => 'الرقم',
                    'reference'       => 'المرجع',
                    'accounting-date' => 'تاريخ المحاسبة',
                    'journal'         => 'اليومية',
                ],
            ],
        ],

        'tabs' => [
            'lines' => [
                'title' => 'عناصر اليومية',

                'repeater' => [
                    'entries' => [
                        'account'  => 'الحساب',
                        'partner'  => 'الشريك',
                        'label'    => 'التسمية',
                        'currency' => 'العملة',
                        'taxes'    => 'الضرائب',
                        'debit'    => 'مدين',
                        'credit'   => 'دائن',
                    ],
                ],
            ],

            'other-information' => [
                'title' => 'معلومات أخرى',

                'fieldset' => [
                    'accounting' => [
                        'title' => 'المحاسبة',

                        'entries' => [
                            'company'         => 'الشركة',
                            'fiscal-position' => 'الموقف الضريبي',
                            'checked'         => 'تم التحقق',
                        ],
                    ],
                ],
            ],

            'term-and-conditions' => [
                'title' => 'الشروط والأحكام',
            ],
        ],
    ],
];
