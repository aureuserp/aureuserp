<?php

return [

    'form' => [
        'section' => [
            'general' => [
                'title'  => 'عام',
                'fields' => [
                    'order'      => 'طلب نقطة البيع',
                    'ordered-at' => 'التاريخ',
                    'customer'   => 'العميل',
                    'cashier'    => 'أمين الصندوق',
                ],
            ],
        ],

        'tabs' => [
            'products' => [
                'title'   => 'المنتجات',
                'columns' => [
                    'product'            => 'المنتج',
                    'lot'                => 'رقم الدفعة/التسلسل',
                    'quantity'           => 'الكمية',
                    'uom'                => 'وحدة القياس',
                    'unit-price'         => 'سعر الوحدة',
                    'discount'           => 'خصم %',
                    'taxes'              => 'الضرائب',
                    'tax-excluded'       => 'غير شامل الضريبة',
                    'tax-included'       => 'شامل الضريبة',
                    'full-product-name'  => 'اسم المنتج الكامل',
                    'customer-note'      => 'ملاحظة العميل',
                    'total-cost'         => 'إجمالي التكلفة',
                    'margin'             => 'هامش الربح',
                    'margin-percent'     => 'هامش الربح (%)',
                    'refunded-quantity'  => 'الكمية المستردة',
                ],

                'actions' => [
                    'open-product' => 'فتح المنتج',
                ],

                'summary' => [
                    'untaxed'  => 'المبلغ غير الخاضع للضريبة',
                    'taxes'    => 'الضرائب',
                    'rounding' => 'التقريب',
                    'total'    => 'الإجمالي',
                    'paid'     => 'مدفوع',
                    'change'   => 'الباقي',
                ],
            ],

            'payments' => [
                'title'  => 'الدفعات',
                'add'    => 'إضافة بند',
                'fields' => [
                    'paid-at'         => 'التاريخ',
                    'method'          => 'طريقة الدفع',
                    'amount'          => 'المبلغ',
                    'card-type'       => 'رقم البطاقة (آخر 4 أرقام)',
                    'card-brand'      => 'علامة البطاقة',
                    'cardholder-name' => 'اسم صاحب البطاقة',
                ],
            ],

            'extra-info' => [
                'title'  => 'معلومات إضافية',
                'fields' => [
                    'receipt-number'  => 'رقم الإيصال',
                    'tracking-number' => 'رقم التتبع',
                    'email'           => 'البريد الإلكتروني',
                    'mobile'          => 'الجوال',
                ],
            ],

            'notes' => [
                'title' => 'ملاحظات عامة',
            ],
        ],
    ],

    'infolist' => [
        'section' => [
            'general' => [
                'title'   => 'عام',
                'entries' => [
                    'order'         => 'طلب نقطة البيع',
                    'customer'      => 'العميل',
                    'session'       => 'جلسة',
                    'ordered-at'    => 'تاريخ الطلب',
                    'point-of-sale' => 'نقطة البيع',
                ],
            ],
        ],

        'tabs' => [
            'order-line' => [
                'title'   => 'بند الطلب',
                'entries' => [
                    'product'    => 'المنتج',
                    'quantity'   => 'الكمية',
                    'unit-price' => 'سعر الوحدة',
                    'taxes'      => 'الضرائب',
                    'discount'   => 'الخصم (%)',
                    'amount'     => 'المبلغ',
                ],
                'totals' => [
                    'untaxed' => 'المبلغ غير الخاضع للضريبة',
                    'taxes'   => 'الضرائب',
                    'total'   => 'إجمالي المبلغ',
                    'margin'  => 'هامش الربح',
                ],
            ],

            'payments' => [
                'title'   => 'الدفعات',
                'entries' => [
                    'method'  => 'طريقة الدفع',
                    'amount'  => 'المبلغ',
                    'paid-at' => 'تاريخ الدفع',
                ],
            ],

            'extra-info' => [
                'title'   => 'معلومات إضافية',
                'entries' => [
                    'receipt-number'  => 'رقم الإيصال',
                    'tracking-number' => 'رقم التتبع',
                    'email'           => 'البريد الإلكتروني',
                    'mobile'          => 'الجوال',
                ],
            ],

            'other-information' => [
                'title'   => 'معلومات أخرى',
                'entries' => [
                    'reference'      => 'المرجع',
                    'receipt-number' => 'رقم الإيصال',
                    'operation'      => 'العملية',
                    'cashier'        => 'أمين الصندوق',
                    'paid'           => 'مدفوع',
                    'change'         => 'الباقي',
                ],
            ],
        ],
    ],
    'navigation' => [
        'title' => 'الطلبات',
        'group' => 'نقطة البيع',
    ],

    'table' => [
        'columns' => [
            'name'                 => 'مرجع الطلب',
            'reference'            => 'رقم الإيصال',
            'session'              => 'جلسة',
            'config'               => 'نقطة البيع',
            'partner'              => 'العميل',
            'ordered-at'           => 'التاريخ',
            'amount-total'         => 'الإجمالي',
            'amount-paid'          => 'مدفوع',
            'user'                 => 'أمين الصندوق',
            'has-failed-operation' => 'عملية فاشلة',
            'is-invoiced'          => 'مفوتر',
            'is-edited'            => 'مُعدّل',
            'tracking-number'      => 'رقم التتبع',
            'state'                => 'الحالة',
        ],

        'groups' => [
            'session'    => 'جلسة',
            'config'     => 'نقطة البيع',
            'state'      => 'الحالة',
            'ordered-at' => 'تاريخ الطلب',
        ],

        'filters' => [
            'state'   => 'الحالة',
            'session' => 'جلسة',
            'config'  => 'نقطة البيع',
        ],
    ],
];
