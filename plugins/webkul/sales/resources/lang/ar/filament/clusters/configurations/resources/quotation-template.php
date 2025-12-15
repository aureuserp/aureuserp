<?php

return [
    'title' => 'قالب عرض السعر',

    'navigation' => [
        'title'  => 'قالب عرض السعر',
        'group'  => 'أوامر البيع',
    ],

    'form' => [
        'tabs' => [
            'products' => [
                'title'  => 'المنتجات',
                'fields' => [
                    'products'     => 'المنتجات',
                    'name'         => 'الاسم',
                    'quantity'     => 'الكمية',
                ],
            ],

            'terms-and-conditions' => [
                'title'  => 'الشروط والأحكام',
                'fields' => [
                    'note-placeholder' => 'اكتب الشروط والأحكام الخاصة بعروض الأسعار.',
                ],
            ],
        ],

        'sections' => [
            'general' => [
                'title' => 'المعلومات العامة',

                'fields' => [
                    'name'               => 'الاسم',
                    'quotation-validity' => 'صلاحية عرض السعر',
                    'sale-journal'       => 'دفتر يومية المبيعات',
                ],
            ],

            'signature-and-payment' => [
                'title' => 'التوقيع والدفعات',

                'fields' => [
                    'online-signature'      => 'التوقيع الإلكتروني',
                    'online-payment'        => 'الدفع الإلكتروني',
                    'prepayment-percentage' => 'نسبة الدفع المسبق',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'created-by'            => 'أنشئ بواسطة',
            'company'               => 'الشركة',
            'name'                  => 'الاسم',
            'number-of-days'        => 'عدد الأيام',
            'journal'               => 'دفتر يومية المبيعات',
            'signature-required'    => 'التوقيع مطلوب',
            'payment-required'      => 'الدفع مطلوب',
            'prepayment-percentage' => 'نسبة الدفع المسبق',
        ],
        'groups'  => [
            'company' => 'الشركة',
            'name'    => 'الاسم',
            'journal' => 'دفتر اليومية',
        ],
        'filters' => [
            'created-by' => 'أنشئ بواسطة',
            'company'    => 'الشركة',
            'name'       => 'الاسم',
            'created-at' => 'تاريخ الإنشاء',
            'updated-at' => 'تاريخ التحديث',
        ],
        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'تم حذف قالب عرض السعر',
                    'body'  => 'تم حذف قالب عرض السعر بنجاح.',
                ],
            ],

        ],
        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'تم حذف قالب عرض السعر',
                    'body'  => 'تم حذف قالب عرض السعر بنجاح.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'tabs' => [
            'products' => [
                'title' => 'المنتجات',
            ],
            'terms-and-conditions' => [
                'title' => 'الشروط والأحكام',
            ],
        ],
        'sections' => [
            'general' => [
                'title' => 'المعلومات العامة',
            ],
            'signature_and_payment' => [
                'title' => 'التوقيع والدفع',
            ],
        ],
        'entries' => [
            'product'               => 'المنتج',
            'description'           => 'الوصف',
            'quantity'              => 'الكمية',
            'unit-price'            => 'سعر الوحدة',
            'section-name'          => 'اسم القسم',
            'note-title'            => 'عنوان الملاحظة',
            'name'                  => 'اسم القالب',
            'quotation-validity'    => 'صلاحية عرض السعر',
            'sale-journal'          => 'دفتر يومية المبيعات',
            'online-signature'      => 'التوقيع الإلكتروني',
            'online-payment'        => 'الدفع الإلكتروني',
            'prepayment-percentage' => 'نسبة الدفع المسبق',
        ],
    ],
];
