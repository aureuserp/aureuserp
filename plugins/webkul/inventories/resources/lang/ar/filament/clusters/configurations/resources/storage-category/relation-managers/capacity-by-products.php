<?php

return [
    'title' => 'السعة حسب المنتجات',

    'form' => [
        'product' => 'المنتج',
        'qty'     => 'الكمية',
    ],

    'table' => [
        'header-actions' => [
            'create' => [
                'label' => 'إضافة سعة منتج',

                'notification' => [
                    'title' => 'تم إنشاء سعة المنتج',
                    'body'  => 'تم إضافة سعة المنتج بنجاح.',
                ],
            ],
        ],

        'columns' => [
            'product' => 'المنتج',
            'qty'     => 'الكمية',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'تم تحديث سعة المنتج',
                    'body'  => 'تم تحديث سعة المنتج بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف سعة المنتج',
                    'body'  => 'تم حذف سعة المنتج بنجاح.',
                ],
            ],
        ],
    ],
];
