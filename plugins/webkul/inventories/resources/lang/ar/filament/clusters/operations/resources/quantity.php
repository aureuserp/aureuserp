<?php

return [
    'navigation' => [
        'title' => 'الكميات',
        'group' => 'التعديلات',
    ],

    'form' => [
        'fields' => [
            'location'         => 'الموقع',
            'product'          => 'المنتج',
            'package'          => 'الطرد',
            'lot'              => 'الدفعة / الأرقام التسلسلية',
            'counted-qty'      => 'الكمية المحسوبة',
            'scheduled-at'     => 'موعد الجدولة',
            'storage-category' => 'فئة التخزين',
        ],
    ],

    'table' => [
        'columns' => [
            'location'           => 'الموقع',
            'product'            => 'المنتج',
            'product-category'   => 'فئة المنتج',
            'lot'                => 'الدفعة / الأرقام التسلسلية',
            'storage-category'   => 'فئة التخزين',
            'available-quantity' => 'الكمية المتاحة',
            'quantity'           => 'الكمية',
            'package'            => 'الطرد',
            'last-counted-at'    => 'آخر جرد في',
            'on-hand'            => 'الكمية المتاحة',
            'counted'            => 'الكمية المحسوبة',
            'difference'         => 'الفرق',
            'scheduled-at'       => 'موعد الجدولة',
            'user'               => 'المستخدم',
            'company'            => 'الشركة',

            'on-hand-before-state-updated' => [
                'notification' => [
                    'title' => 'تم تحديث الكمية',
                    'body'  => 'تم تحديث الكمية بنجاح.',
                ],
            ],
        ],

        'groups' => [
            'product'          => 'المنتج',
            'product-category' => 'فئة المنتج',
            'location'         => 'الموقع',
            'storage-category' => 'فئة التخزين',
            'lot'              => 'الدفعة / الأرقام التسلسلية',
            'company'          => 'الشركة',
            'package'          => 'الطرد',
        ],

        'filters' => [
            'product'             => 'المنتج',
            'uom'                 => 'وحدة القياس',
            'product-category'    => 'فئة المنتج',
            'location'            => 'الموقع',
            'storage-category'    => 'فئة التخزين',
            'lot'                 => 'الدفعة / الأرقام التسلسلية',
            'company'             => 'الشركة',
            'package'             => 'الطرد',
            'on-hand-quantity'    => 'الكمية المتاحة',
            'difference-quantity' => 'كمية الفرق',
            'incoming-at'         => 'تاريخ الوارد',
            'scheduled-at'        => 'موعد الجدولة',
            'user'                => 'المستخدم',
            'created-at'          => 'تاريخ الإنشاء',
            'updated-at'          => 'تاريخ التحديث',
            'company'             => 'الشركة',
            'creator'             => 'المُنشئ',
        ],

        'header-actions' => [
            'create' => [
                'label' => 'إضافة كمية',

                'notification' => [
                    'title' => 'تمت إضافة الكمية',
                    'body'  => 'تمت إضافة الكمية بنجاح.',
                ],

                'before' => [
                    'notification' => [
                        'title' => 'الكمية موجودة بالفعل',
                        'body'  => 'توجد كمية بالفعل لهذه الإعدادات. يرجى تحديث الكمية الموجودة بدلاً من ذلك.',
                    ],
                ],
            ],
        ],

        'actions' => [
            'apply' => [
                'label' => 'تطبيق',

                'notification' => [
                    'title' => 'تم تطبيق تغييرات الكمية',
                    'body'  => 'تم تطبيق تغييرات الكمية بنجاح.',
                ],
            ],

            'clear' => [
                'label' => 'مسح',

                'notification' => [
                    'title' => 'تم مسح تغييرات الكمية',
                    'body'  => 'تم مسح تغييرات الكمية بنجاح.',
                ],
            ],
        ],
    ],
];
