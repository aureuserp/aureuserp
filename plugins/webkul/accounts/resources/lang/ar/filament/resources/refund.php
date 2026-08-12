<?php

return [
    'title' => 'فاتورة',

    'navigation' => [
        'title' => 'الفواتير',
        'group' => 'الفواتير',
    ],

    'form' => [
        'section' => [
            'general' => [
                'title'  => 'عام',
                'fields' => [
                    'vendor-credit-note' => 'إشعار دائن المورد',
                    'vendor'             => 'المورد',
                    'bill-date'          => 'تاريخ الفاتورة',
                    'bill-reference'     => 'مرجع الفاتورة',
                    'accounting-date'    => 'التاريخ المحاسبي',
                    'payment-reference'  => 'مرجع الدفع',
                    'recipient-bank'     => 'بنك المستفيد',
                    'due-date'           => 'تاريخ الاستحقاق',
                    'payment-term'       => 'شروط الدفع',
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
                            'incoterm'          => 'شروط التجارة الدولية',
                            'incoterm-location' => 'موقع شروط التجارة الدولية',
                        ],
                    ],

                    'secured' => [
                        'title'  => 'مؤمّن',
                        'fields' => [
                            'payment-method' => 'طريقة الدفع',
                            'auto-post'      => 'ترحيل تلقائي',
                            'checked'        => 'تم التحقق',
                        ],
                    ],

                    'additional-information' => [
                        'title'  => 'معلومات إضافية',
                        'fields' => [
                            'company'  => 'الشركة',
                            'currency' => 'العملة',
                        ],
                    ],
                ],
            ],

            'term-and-conditions' => [
                'title' => 'الشروط والأحكام',
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
                    'accounting-date'   => 'التاريخ المحاسبي',
                    'payment-reference' => 'مرجع الدفع',
                    'recipient-bank'    => 'بنك المستفيد',
                    'due-date'          => 'تاريخ الاستحقاق',
                    'payment-term'      => 'شروط الدفع',
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
                            'incoterm'          => 'شروط التجارة الدولية',
                            'incoterm-location' => 'موقع شروط التجارة الدولية',
                        ],
                    ],

                    'secured' => [
                        'title'   => 'مؤمّن',
                        'entries' => [
                            'payment-method' => 'طريقة الدفع',
                            'auto-post'      => 'ترحيل تلقائي',
                            'checked'        => 'تم التحقق',
                        ],
                    ],

                    'additional-information' => [
                        'title'   => 'معلومات إضافية',
                        'entries' => [
                            'company'  => 'الشركة',
                            'currency' => 'العملة',
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
