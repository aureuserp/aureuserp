<?php

return [
    'navigation' => [
        'title' => 'الخردة',
        'group' => 'التعديلات',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'عام',

                'fields' => [
                    'product'              => 'المنتج',
                    'package'              => 'الطرد',
                    'quantity'             => 'الكمية',
                    'unit'                 => 'وحدة القياس',
                    'lot'                  => 'الدفعة/التسلسلي',
                    'tags'                 => 'الوسوم',
                    'name'                 => 'الاسم',
                    'color'                => 'اللون',
                    'owner'                => 'المالك',
                    'source-location'      => 'موقع المصدر',
                    'destination-location' => 'موقع الخردة',
                    'source-document'      => 'المستند المصدر',
                    'company'              => 'الشركة',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'date'            => 'التاريخ',
            'reference'       => 'المرجع',
            'product'         => 'المنتج',
            'package'         => 'الطرد',
            'quantity'        => 'الكمية',
            'uom'             => 'وحدة القياس',
            'source-location' => 'موقع المصدر',
            'scrap-location'  => 'موقع الخردة',
            'unit'            => 'وحدة القياس',
            'lot'             => 'الدفعة/التسلسلي',
            'tags'            => 'الوسوم',
            'state'           => 'الحالة',
        ],

        'groups' => [
            'product'              => 'المنتج',
            'source-location'      => 'موقع المصدر',
            'destination-location' => 'موقع الخردة',
        ],

        'filters' => [
            'source-location'      => 'موقع المصدر',
            'destination-location' => 'موقع الخردة',
            'product'              => 'المنتج',
            'state'                => 'الحالة',
            'product-category'     => 'فئة المنتج',
            'uom'                  => 'وحدة القياس',
            'lot'                  => 'الدفعة/التسلسلي',
            'package'              => 'الطرد',
            'tags'                 => 'الوسوم',
            'company'              => 'الشركة',
            'quantity'             => 'الكمية',
            'creator'              => 'المُنشئ',
            'closed-at'            => 'تاريخ الإغلاق',
            'created-at'           => 'تاريخ الإنشاء',
            'updated-at'           => 'تاريخ التحديث',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'تم حذف الخردة',
                        'body'  => 'تم حذف الخردة بنجاح.',
                    ],

                    'error' => [
                        'title' => 'تعذر حذف الخردة',
                        'body'  => 'لا يمكن حذف الخردة لأنها قيد الاستخدام حالياً.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'تم حذف الخردة',
                        'body'  => 'تم حذف الخردة المحددة بنجاح.',
                    ],

                    'error' => [
                        'title' => 'تعذر حذف الخردة',
                        'body'  => 'لا يمكن حذف الخردة لأنها قيد الاستخدام حالياً.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'تفاصيل الخردة',

                'entries' => [
                    'product'              => 'المنتج',
                    'quantity'             => 'الكمية',
                    'lot'                  => 'الدفعة',
                    'tags'                 => 'الوسوم',
                    'package'              => 'الطرد',
                    'owner'                => 'المالك',
                    'source-location'      => 'موقع المصدر',
                    'destination-location' => 'موقع الوجهة',
                    'source-document'      => 'المستند المصدر',
                    'company'              => 'الشركة',
                ],
            ],

            'record-information' => [
                'title' => 'معلومات السجل',

                'entries' => [
                    'created-by'   => 'أنشئ بواسطة',
                    'created-at'   => 'تاريخ الإنشاء',
                    'last-updated' => 'آخر تحديث',
                ],
            ],
        ],
    ],
];
