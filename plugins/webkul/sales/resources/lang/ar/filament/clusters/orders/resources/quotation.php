<?php

return [
    'title' => 'عرض سعر',

    'navigation' => [
        'title' => 'عروض الأسعار',
    ],

    'form' => [
        'section' => [
            'general' => [
                'title'  => 'عام',
                'fields' => [
                    'customer'       => 'العميل',
                    'expiration'     => 'تاريخ الانتهاء',
                    'quotation-date' => 'تاريخ عرض السعر',
                    'order-date'     => 'تاريخ الطلب',
                    'payment-term'   => 'شروط الدفع',
                ],
            ],
        ],

        'tabs' => [
            'order-line' => [
                'title' => 'بنود الطلب',

                'repeater' => [
                    'products' => [
                        'title'       => 'المنتجات',
                        'add-product' => 'إضافة منتج',

                        'columns'     => [
                            'product'             => 'المنتج',
                            'product-variants'    => 'متغيرات المنتج',
                            'product-simple'      => 'منتج بسيط',
                            'quantity'            => 'الكمية',
                            'uom'                 => 'وحدة القياس',
                            'lead-time'           => 'وقت التسليم',
                            'qty-delivered'       => 'الكمية المسلّمة',
                            'qty-invoiced'        => 'الكمية المفوترة',
                            'packaging-qty'       => 'كمية التغليف',
                            'packaging'           => 'التغليف',
                            'unit-price'          => 'سعر الوحدة',
                            'cost'                => 'التكلفة',
                            'margin'              => 'الهامش',
                            'taxes'               => 'الضرائب',
                            'amount'              => 'المبلغ',
                            'margin-percentage'   => 'الهامش (%)',
                            'discount-percentage' => 'الخصم (%)',
                        ],

                        'fields'      => [
                            'product'             => 'المنتج',
                            'product-variants'    => 'متغيرات المنتج',
                            'product-simple'      => 'منتج بسيط',
                            'quantity'            => 'الكمية',
                            'uom'                 => 'وحدة القياس',
                            'lead-time'           => 'وقت التسليم',
                            'qty-delivered'       => 'الكمية المسلّمة',
                            'qty-invoiced'        => 'الكمية المفوترة',
                            'packaging-qty'       => 'كمية التغليف',
                            'packaging'           => 'التغليف',
                            'unit-price'          => 'سعر الوحدة',
                            'cost'                => 'التكلفة',
                            'margin'              => 'الهامش',
                            'taxes'               => 'الضرائب',
                            'amount'              => 'المبلغ',
                            'margin-percentage'   => 'الهامش (%)',
                            'discount-percentage' => 'الخصم (%)',
                        ],
                    ],

                    'product-optional' => [
                        'title'       => 'المنتجات الاختيارية',
                        'add-product' => 'إضافة منتج',

                        'columns' => [
                            'product'             => 'المنتج',
                            'description'         => 'الوصف',
                            'quantity'            => 'الكمية',
                            'uom'                 => 'وحدة القياس',
                            'unit-price'          => 'سعر الوحدة',
                            'discount-percentage' => 'الخصم (%)',
                        ],

                        'fields'      => [
                            'product'             => 'المنتج',
                            'description'         => 'الوصف',
                            'quantity'            => 'الكمية',
                            'uom'                 => 'وحدة القياس',
                            'unit-price'          => 'سعر الوحدة',
                            'discount-percentage' => 'الخصم (%)',

                            'actions' => [
                                'tooltip' => [
                                    'add-order-line' => 'إضافة بند طلب',
                                ],

                                'notifications' => [
                                    'product-added' => [
                                        'title' => 'تمت إضافة المنتج',
                                        'body'  => 'تمت إضافة المنتج بنجاح.',
                                    ],

                                    'product-not-found' => [
                                        'title' => 'المنتج غير موجود',
                                    ],

                                    'product-already-exists' => [
                                        'title' => 'المنتج موجود مسبقاً',
                                        'body'  => 'هذا المنتج موجود بالفعل في بنود الطلب. يرجى تحديث البند الحالي بدلاً من ذلك.',
                                    ],

                                    'missing-product-data' => [
                                        'title' => 'بيانات المنتج مفقودة',
                                        'body'  => 'تعذر معالجة المنتج المحدد.',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'other-information' => [
                'title' => 'معلومات أخرى',

                'fieldset' => [
                    'sales' => [
                        'title' => 'المبيعات',

                        'fields' => [
                            'sales-person'       => 'مندوب المبيعات',
                            'customer-reference' => 'مرجع العميل',
                            'tags'               => 'الوسوم',
                        ],
                    ],

                    'shipping' => [
                        'title'  => 'الشحن',
                        'fields' => [
                            'commitment-date' => 'تاريخ التسليم',
                        ],
                    ],

                    'tracking' => [
                        'title'  => 'التتبع',
                        'fields' => [
                            'source-document' => 'المستند المصدر',
                            'medium'          => 'الوسيط',
                            'source'          => 'المصدر',
                            'campaign'        => 'الحملة',
                        ],
                    ],

                    'additional-information' => [
                        'title' => 'معلومات إضافية',

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

    'table' => [
        'columns' => [
            'number'             => 'الرقم',
            'status'             => 'الحالة',
            'invoice-status'     => 'حالة الفاتورة',
            'creation-date'      => 'تاريخ الإنشاء',
            'commitment-date'    => 'تاريخ الالتزام',
            'expected-date'      => 'التاريخ المتوقع',
            'customer'           => 'العميل',
            'sales-person'       => 'مندوب المبيعات',
            'sales-team'         => 'فريق المبيعات',
            'untaxed-amount'     => 'المبلغ بدون ضريبة',
            'amount-tax'         => 'مبلغ الضريبة',
            'amount-total'       => 'المبلغ الإجمالي',
            'customer-reference' => 'مرجع العميل',
        ],

        'filters' => [
            'sales-person'     => 'مندوب المبيعات',
            'utm-source'       => 'مصدر UTM',
            'company'          => 'الشركة',
            'customer'         => 'العميل',
            'journal'          => 'دفتر اليومية',
            'invoice-address'  => 'عنوان الفاتورة',
            'shipping-address' => 'عنوان الشحن',
            'fiscal-position'  => 'المركز المالي',
            'payment-term'     => 'شروط الدفع',
            'currency'         => 'العملة',
            'created-at'       => 'تاريخ الإنشاء',
            'updated-at'       => 'تاريخ التحديث',
        ],

        'groups' => [
            'medium'          => 'الوسيط',
            'source'          => 'المصدر',
            'team'            => 'الفريق',
            'sales-person'    => 'مندوب المبيعات',
            'currency'        => 'العملة',
            'company'         => 'الشركة',
            'customer'        => 'العميل',
            'quotation-date'  => 'تاريخ عرض السعر',
            'commitment-date' => 'تاريخ الالتزام',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'تم استعادة عرض السعر',
                    'body'  => 'تم استعادة عرض السعر بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف عرض السعر',
                    'body'  => 'تم حذف عرض السعر بنجاح.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'تم حذف عرض السعر نهائياً',
                    'body'  => 'تم حذف عرض السعر نهائياً بنجاح.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'تم استعادة عروض الأسعار',
                    'body'  => 'تم استعادة عروض الأسعار بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف عروض الأسعار',
                    'body'  => 'تم حذف عروض الأسعار بنجاح.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'تم حذف عروض الأسعار نهائياً',
                    'body'  => 'تم حذف عروض الأسعار نهائياً بنجاح.',
                ],
            ],
        ],

        'empty-state-action' => [
            'create' => [
                'notification' => [
                    'title' => 'تم إنشاء عروض الأسعار',
                    'body'  => 'تم إنشاء عروض الأسعار بنجاح.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'section' => [
            'general' => [
                'title'   => 'عام',
                'entries' => [
                    'sale-order'     => 'أمر البيع',
                    'customer'       => 'العميل',
                    'expiration'     => 'تاريخ الانتهاء',
                    'quotation-date' => 'تاريخ عرض السعر',
                    'payment-term'   => 'شروط الدفع',
                ],
            ],
        ],

        'tabs' => [
            'order-line' => [
                'title' => 'بنود الطلب',

                'repeater' => [
                    'products' => [
                        'title'       => 'المنتجات',
                        'add-product' => 'إضافة منتج',
                        'entries'     => [
                            'product'             => 'المنتج',
                            'product-variants'    => 'متغيرات المنتج',
                            'product-simple'      => 'منتج بسيط',
                            'quantity'            => 'الكمية',
                            'uom'                 => 'وحدة القياس',
                            'lead-time'           => 'وقت التسليم',
                            'packaging-qty'       => 'كمية التغليف',
                            'packaging'           => 'التغليف',
                            'unit-price'          => 'سعر الوحدة',
                            'cost'                => 'التكلفة',
                            'margin'              => 'الهامش',
                            'taxes'               => 'الضرائب',
                            'amount'              => 'المبلغ',
                            'margin-percentage'   => 'الهامش (%)',
                            'discount-percentage' => 'الخصم (%)',
                            'sub-total'           => 'المجموع الفرعي',
                        ],
                    ],

                    'product-optional' => [
                        'title'       => 'المنتجات الاختيارية',
                        'add-product' => 'إضافة منتج',
                        'entries'     => [
                            'product'             => 'المنتج',
                            'description'         => 'الوصف',
                            'quantity'            => 'الكمية',
                            'uom'                 => 'وحدة القياس',
                            'unit-price'          => 'سعر الوحدة',
                            'discount-percentage' => 'الخصم (%)',
                            'sub-total'           => 'المجموع الفرعي',

                            'actions' => [
                                'tooltip' => [
                                    'add-order-line' => 'إضافة بند طلب',
                                ],

                                'notifications' => [
                                    'product-added' => [
                                        'title' => 'تمت إضافة المنتج',
                                        'body'  => 'تمت إضافة المنتج بنجاح.',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'other-information' => [
                'title' => 'معلومات أخرى',

                'fieldset' => [
                    'sales' => [
                        'title' => 'المبيعات',

                        'entries' => [
                            'sales-person'       => 'مندوب المبيعات',
                            'customer-reference' => 'مرجع العميل',
                            'tags'               => 'الوسوم',
                        ],
                    ],

                    'shipping' => [
                        'title'   => 'الشحن',
                        'entries' => [
                            'commitment-date' => 'تاريخ التسليم',
                        ],
                    ],

                    'tracking' => [
                        'title'   => 'التتبع',
                        'entries' => [
                            'source-document' => 'المستند المصدر',
                            'medium'          => 'الوسيط',
                            'source'          => 'المصدر',
                            'campaign'        => 'الحملة',
                        ],
                    ],

                    'additional-information' => [
                        'title' => 'معلومات إضافية',

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
