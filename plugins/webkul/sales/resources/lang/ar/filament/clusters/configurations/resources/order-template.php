<?php

return [
    'title' => 'منتجات قالب الطلب',

    'navigation' => [
        'title' => 'منتجات قالب الطلب',
        'group' => 'أوامر المبيعات',
    ],

    'global-search' => [
        'name'    => 'الاسم',
    ],

    'form' => [
        'fields' => [
            'sort'           => 'الترتيب',
            'order-template' => 'قالب الطلب',
            'company'        => 'الشركة',
            'product'        => 'المنتج',
            'product-uom'    => 'وحدة قياس المنتج',
            'creator'        => 'المنشئ',
            'display-type'   => 'نوع العرض',
            'name'           => 'الاسم',
            'quantity'       => 'الكمية',
        ],
    ],

    'table' => [
        'columns' => [
            'sort'           => 'الترتيب',
            'order-template' => 'قالب الطلب',
            'company'        => 'الشركة',
            'product'        => 'المنتج',
            'product-uom'    => 'وحدة قياس المنتج',
            'created-by'     => 'أنشئ بواسطة',
            'display-type'   => 'نوع العرض',
            'name'           => 'الاسم',
            'quantity'       => 'الكمية',
            'created-at'     => 'تاريخ الإنشاء',
            'updated-at'     => 'تاريخ التحديث',

        ],
        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'تم تحديث منتجات قالب الطلب',
                    'body'  => 'تم تحديث منتجات قالب الطلب بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف منتجات قالب الطلب',
                    'body'  => 'تم حذف منتجات قالب الطلب بنجاح.',
                ],
            ],
        ],
        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'تم حذف منتجات قالب الطلب',
                    'body'  => 'تم حذف منتجات قالب الطلب بنجاح.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'sort'           => 'ترتيب الفرز',
            'order-template' => 'قالب الطلب',
            'company'        => 'الشركة',
            'product'        => 'المنتج',
            'product-uom'    => 'وحدة قياس المنتج',
            'display-type'   => 'نوع العرض',
            'name'           => 'الاسم',
            'quantity'       => 'الكمية',
        ],
    ],
];
