<?php

return [
    'form' => [
        'name'    => 'الاسم',
        'barcode' => 'الباركود',
        'product' => 'المنتج',
        'routes'  => 'المسارات',
        'qty'     => 'الكمية',
        'company' => 'الشركة',
    ],

    'table' => [
        'columns' => [
            'name'       => 'الاسم',
            'product'    => 'المنتج',
            'routes'     => 'المسارات',
            'qty'        => 'الكمية',
            'company'    => 'الشركة',
            'barcode'    => 'الباركود',
            'created-at' => 'تاريخ الإنشاء',
            'updated-at' => 'تاريخ التحديث',
        ],

        'groups' => [
            'product'    => 'المنتج',
            'created-at' => 'تاريخ الإنشاء',
            'updated-at' => 'تاريخ التحديث',
        ],

        'filters' => [
            'product' => 'المنتج',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'تم تحديث التغليف',
                    'body'  => 'تم تحديث التغليف بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'تم حذف التغليف',
                        'body'  => 'تم حذف التغليف بنجاح.',
                    ],

                    'error' => [
                        'title' => 'تعذر حذف التغليف',
                        'body'  => 'لا يمكن حذف التغليف لأنه قيد الاستخدام حالياً.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'print' => [
                'label' => 'طباعة',
            ],

            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'تم حذف التغليفات',
                        'body'  => 'تم حذف التغليفات بنجاح.',
                    ],

                    'error' => [
                        'title' => 'تعذر حذف التغليفات',
                        'body'  => 'لا يمكن حذف التغليفات لأنها قيد الاستخدام حالياً.',
                    ],
                ],
            ],
        ],

        'empty-state-actions' => [
            'create' => [
                'label' => 'تغليف جديد',

                'notification' => [
                    'title' => 'تم إنشاء التغليف',
                    'body'  => 'تم إنشاء التغليف بنجاح.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'معلومات عامة',

                'entries' => [
                    'name'    => 'اسم التغليف',
                    'barcode' => 'الباركود',
                    'product' => 'المنتج',
                    'qty'     => 'الكمية',
                ],
            ],

            'organization' => [
                'title' => 'تفاصيل المؤسسة',

                'entries' => [
                    'company'    => 'الشركة',
                    'creator'    => 'أنشئ بواسطة',
                    'created_at' => 'تاريخ الإنشاء',
                    'updated_at' => 'آخر تحديث',
                ],
            ],
        ],
    ],
];
