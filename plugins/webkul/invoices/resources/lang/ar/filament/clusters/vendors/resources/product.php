<?php

return [
    'navigation' => [
        'title' => 'المنتجات',
        'group' => 'المخزون',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'عام',

                'fields' => [
                    'name'             => 'الاسم',
                    'name-placeholder' => 'مثال: تي شيرت',
                    'description'      => 'الوصف',
                    'tags'             => 'الوسوم',
                    'sales'            => 'المبيعات',
                    'purchase'         => 'المشتريات',
                ],
            ],

            'invoice-policy' => [
                'title'            => 'سياسة الفوترة',
                'ordered-policy'   => 'يمكنك فوترة البضائع قبل تسليمها.',
                'delivered-policy' => 'الفوترة بعد التسليم، بناءً على الكميات المسلمة وليس المطلوبة.',
            ],

            'images' => [
                'title' => 'الصور',
            ],

            'settings' => [
                'title' => 'الإعدادات',

                'fields' => [
                    'type'      => 'النوع',
                    'reference' => 'المرجع',
                    'barcode'   => 'الباركود',
                    'category'  => 'الفئة',
                    'company'   => 'الشركة',
                ],
            ],

            'category-and-tags' => [
                'title' => 'الفئة والوسوم',

                'fields' => [
                    'category' => 'الفئة',
                    'tags'     => 'الوسوم',
                ],
            ],

            'pricing' => [
                'title' => 'التسعير',

                'fields' => [
                    'price' => 'السعر',
                    'cost'  => 'التكلفة',
                ],
            ],

            'additional' => [
                'title' => 'إضافي',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'        => 'الاسم',
            'images'      => 'الصور',
            'type'        => 'النوع',
            'reference'   => 'المرجع',
            'responsible' => 'المسؤول',
            'barcode'     => 'الباركود',
            'category'    => 'الفئة',
            'company'     => 'الشركة',
            'price'       => 'السعر',
            'cost'        => 'التكلفة',
            'tags'        => 'الوسوم',
            'deleted-at'  => 'تاريخ الحذف',
            'created-at'  => 'تاريخ الإنشاء',
            'updated-at'  => 'تاريخ التحديث',
        ],

        'groups' => [
            'type'       => 'النوع',
            'category'   => 'الفئة',
            'created-at' => 'تاريخ الإنشاء',
        ],

        'filters' => [
            'name'        => 'الاسم',
            'type'        => 'النوع',
            'reference'   => 'المرجع',
            'barcode'     => 'الباركود',
            'category'    => 'الفئة',
            'company'     => 'الشركة',
            'price'       => 'السعر',
            'cost'        => 'التكلفة',
            'is-favorite' => 'مفضل',
            'weight'      => 'الوزن',
            'volume'      => 'الحجم',
            'tags'        => 'الوسوم',
            'responsible' => 'المسؤول',
            'created-at'  => 'تاريخ الإنشاء',
            'updated-at'  => 'تاريخ التحديث',
            'creator'     => 'المُنشئ',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'تم استعادة المنتج',
                    'body'  => 'تم استعادة المنتج بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف المنتج',
                    'body'  => 'تم حذف المنتج بنجاح.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'تم حذف المنتج نهائياً',
                    'body'  => 'تم حذف المنتج نهائياً بنجاح.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'تم استعادة المنتجات',
                    'body'  => 'تم استعادة المنتجات بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف المنتجات',
                    'body'  => 'تم حذف المنتجات بنجاح.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'تم حذف المنتجات نهائياً',
                    'body'  => 'تم حذف المنتجات نهائياً بنجاح.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'عام',

                'entries' => [
                    'name'             => 'الاسم',
                    'name-placeholder' => 'مثال: تي شيرت',
                    'description'      => 'الوصف',
                    'tags'             => 'الوسوم',
                ],
            ],

            'images' => [
                'title' => 'الصور',

                'entries' => [],
            ],

            'settings' => [
                'title' => 'الإعدادات',

                'entries' => [
                    'type'      => 'النوع',
                    'reference' => 'المرجع',
                    'barcode'   => 'الباركود',
                    'category'  => 'الفئة',
                    'company'   => 'الشركة',
                ],
            ],

            'pricing' => [
                'title' => 'التسعير',

                'entries' => [
                    'price' => 'السعر',
                    'cost'  => 'التكلفة',
                ],
            ],

            'inventory' => [
                'title' => 'المخزون',

                'entries' => [],

                'fieldsets' => [
                    'tracking' => [
                        'title' => 'التتبع',

                        'entries' => [
                            'track-inventory' => 'تتبع المخزون',
                            'track-by'        => 'التتبع بواسطة',
                            'expiration-date' => 'تاريخ انتهاء الصلاحية',
                        ],
                    ],

                    'operation' => [
                        'title' => 'العمليات',

                        'entries' => [
                            'routes' => 'المسارات',
                        ],
                    ],

                    'logistics' => [
                        'title' => 'اللوجستيات',

                        'entries' => [
                            'responsible' => 'المسؤول',
                            'weight'      => 'الوزن',
                            'volume'      => 'الحجم',
                            'sale-delay'  => 'مهلة العميل (أيام)',
                        ],
                    ],

                    'traceability' => [
                        'title' => 'التتبع',

                        'entries' => [
                            'expiration-date'  => 'تاريخ انتهاء الصلاحية (أيام)',
                            'best-before-date' => 'تاريخ الاستخدام الأفضل (أيام)',
                            'removal-date'     => 'تاريخ الإزالة (أيام)',
                            'alert-date'       => 'تاريخ التنبيه (أيام)',
                        ],
                    ],
                ],
            ],

            'record-information' => [
                'title' => 'معلومات السجل',

                'entries' => [
                    'created-at' => 'تاريخ الإنشاء',
                    'created-by' => 'أنشئ بواسطة',
                    'updated-at' => 'تاريخ التحديث',
                ],
            ],
        ],
    ],
];
