<?php

return [
    'navigation' => [
        'title' => 'اتفاقيات الشراء',
        'group' => 'الشراء',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'عام',

                'fields' => [
                    'vendor'                => 'المورد',
                    'valid-from'            => 'صالح من',
                    'valid-to'              => 'صالح حتى',
                    'buyer'                 => 'المشتري',
                    'reference'             => 'المرجع',
                    'reference-placeholder' => 'مثال: PO/123',
                    'agreement-type'        => 'نوع الاتفاقية',
                    'company'               => 'الشركة',
                    'currency'              => 'العملة',
                ],
            ],
        ],

        'tabs' => [
            'products' => [
                'title' => 'المنتجات',

                'columns' => [
                    'product'    => 'المنتج',
                    'quantity'   => 'الكمية',
                    'ordered'    => 'المطلوب',
                    'uom'        => 'وحدة القياس',
                    'unit-price' => 'سعر الوحدة',
                ],

                'fields' => [
                    'product'    => 'المنتج',
                    'quantity'   => 'الكمية',
                    'ordered'    => 'المطلوب',
                    'uom'        => 'وحدة القياس',
                    'unit-price' => 'سعر الوحدة',
                ],
            ],

            'additional' => [
                'title' => 'معلومات إضافية',
            ],

            'terms' => [
                'title' => 'الشروط والأحكام',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'agreement'      => 'الاتفاقية',
            'vendor'         => 'المورد',
            'agreement-type' => 'نوع الاتفاقية',
            'buyer'          => 'المشتري',
            'company'        => 'الشركة',
            'valid-from'     => 'صالح من',
            'valid-to'       => 'صالح حتى',
            'reference'      => 'المرجع',
            'status'         => 'الحالة',
        ],

        'groups' => [
            'agreement-type' => 'نوع الاتفاقية',
            'vendor'         => 'المورد',
            'state'          => 'الحالة',
            'created-at'     => 'تاريخ الإنشاء',
            'updated-at'     => 'تاريخ التحديث',
        ],

        'filters' => [
            'agreement'      => 'الاتفاقية',
            'vendor'         => 'المورد',
            'agreement-type' => 'نوع الاتفاقية',
            'buyer'          => 'المشتري',
            'company'        => 'الشركة',
            'valid-from'     => 'صالح من',
            'valid-to'       => 'صالح حتى',
            'reference'      => 'المرجع',
            'status'         => 'الحالة',
            'created-at'     => 'تاريخ الإنشاء',
            'updated-at'     => 'تاريخ التحديث',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'تم حذف اتفاقية الشراء',
                    'body'  => 'تم حذف اتفاقية الشراء بنجاح.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'تم استعادة اتفاقية الشراء',
                    'body'  => 'تم استعادة اتفاقية الشراء بنجاح.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'تم حذف اتفاقية الشراء نهائياً',
                        'body'  => 'تم حذف اتفاقية الشراء نهائياً بنجاح.',
                    ],

                    'error' => [
                        'title' => 'تعذر حذف اتفاقية الشراء',
                        'body'  => 'لا يمكن حذف اتفاقية الشراء لأنها قيد الاستخدام حالياً.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'تم حذف اتفاقيات الشراء',
                    'body'  => 'تم حذف اتفاقيات الشراء بنجاح.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'تم استعادة اتفاقيات الشراء',
                    'body'  => 'تم استعادة اتفاقيات الشراء بنجاح.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'تم حذف اتفاقيات الشراء نهائياً',
                        'body'  => 'تم حذف اتفاقيات الشراء نهائياً بنجاح.',
                    ],

                    'error' => [
                        'title' => 'تعذر حذف اتفاقيات الشراء',
                        'body'  => 'لا يمكن حذف اتفاقيات الشراء لأنها قيد الاستخدام حالياً.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'عام',

                'entries' => [
                    'vendor'                => 'المورد',
                    'valid-from'            => 'صالح من',
                    'valid-to'              => 'صالح حتى',
                    'buyer'                 => 'المشتري',
                    'reference'             => 'المرجع',
                    'reference-placeholder' => 'مثال: PO/123',
                    'agreement-type'        => 'نوع الاتفاقية',
                    'company'               => 'الشركة',
                    'currency'              => 'العملة',
                ],
            ],

            'metadata' => [
                'title' => 'البيانات الوصفية',

                'entries' => [
                    'created-at' => 'تاريخ الإنشاء',
                    'created-by' => 'أنشئ بواسطة',
                    'updated-at' => 'تاريخ التحديث',
                ],
            ],
        ],

        'tabs' => [
            'products' => [
                'title' => 'المنتجات',

                'entries' => [
                    'product'    => 'المنتج',
                    'quantity'   => 'الكمية',
                    'ordered'    => 'المطلوب',
                    'uom'        => 'وحدة القياس',
                    'unit-price' => 'سعر الوحدة',
                ],
            ],

            'additional' => [
                'title' => 'معلومات إضافية',
            ],

            'terms' => [
                'title' => 'الشروط والأحكام',
            ],
        ],
    ],
];
