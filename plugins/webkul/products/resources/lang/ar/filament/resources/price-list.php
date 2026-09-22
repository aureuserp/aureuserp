<?php

return [
    'navigation' => [
        'title' => 'قوائم الأسعار',
        'group' => 'المنتجات',
    ],

    'header-actions' => [
        'create' => [
            'label' => 'قائمة أسعار جديدة',
        ],
    ],

    'form' => [
        'section' => [
            'general' => [
                'title' => 'معلومات عامة',

                'fields' => [
                    'name'     => 'اسم قائمة الأسعار',
                    'currency' => 'العملة',
                    'company'  => 'الشركة',
                    'status'   => 'نشط',
                ],
            ],

            'rules' => [
                'title'        => 'قواعد الأسعار',
                'description'  => 'أول قاعدة تطابق المنتج تحدد سعره. القواعد الأكثر تحديداً تتفوق على الأعم.',
                'add-rule'     => 'إضافة قاعدة',
                'all-products' => 'كل المنتجات',

                'actions' => [
                    'edit' => 'تعديل القاعدة',
                ],

                'columns' => [
                    'apply-to'     => 'تطبق على',
                    'target'       => 'تنطبق على',
                    'min-quantity' => 'أقل كمية',
                    'type'         => 'طريقة حساب السعر',
                    'price'        => 'السعر',
                    'period'       => 'الفترة',
                ],

                'formula' => [
                    'discount'     => 'خصم',
                    'markup'       => 'زيادة',
                    'rule-tip'     => ':base مع :discount % :type ورسوم إضافية :surcharge'."\n".'مثال: :amount * :factor + :surcharge ← :total',
                    'rounding-tip' => 'نصيحة: تريد التقريب إلى 9.99؟ قرّب إلى 10.00 واضبط رسماً إضافياً بـ -0.01.',
                ],

                'fields' => [
                    'apply-to-product'      => 'منتج',
                    'apply-to-category'     => 'فئة',
                    'all-products'          => 'كل المنتجات',
                    'all-categories'        => 'كل الفئات',
                    'all-variants'          => 'كل المتغيرات',
                    'variant'               => 'متغير',
                    'on'                    => 'على',
                    'sales-price'           => 'سعر البيع',
                    'apply-to'              => 'تطبق على',
                    'product'               => 'المنتج',
                    'category'              => 'فئة المنتج',
                    'min-quantity'          => 'أقل كمية',
                    'type'                  => 'نوع السعر',
                    'fixed-price'           => 'سعر ثابت',
                    'percent-price'         => 'خصم',
                    'percent-price-helper'  => 'استخدم قيمة سالبة لتطبيق زيادة.',
                    'base'                  => 'السعر الأساسي',
                    'base-price-list'       => 'قائمة أسعار أخرى',
                    'price-discount'        => 'خصم',
                    'price-discount-helper' => 'استخدم قيمة سالبة لتطبيق زيادة.',
                    'price-markup'          => 'زيادة',
                    'price-round'           => 'تقريب إلى',
                    'price-round-helper'    => 'يقرّب السعر إلى مضاعف هذه القيمة، بعد الخصم وقبل الرسوم الإضافية.',
                    'price-surcharge'       => 'رسوم إضافية',
                    'price-min-margin'      => 'أدنى هامش',
                    'price-max-margin'      => 'أقصى هامش',
                    'starts-at'             => 'فترة الصلاحية',
                    'ends-at'               => 'تاريخ الانتهاء',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'     => 'قائمة الأسعار',
            'currency' => 'العملة',
            'company'  => 'الشركة',
            'rules'    => 'القواعد',
            'status'   => 'نشط',
        ],

        'filters' => [
            'status'   => 'نشط',
            'currency' => 'العملة',
        ],
    ],
];
