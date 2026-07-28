<?php

return [
    'navigation' => [
        'title' => 'قواعد التخزين',
        'group' => 'إدارة المستودعات',
    ],

    'form' => [
        'fields' => [
            'in-location'          => 'عند وصول المنتج إلى',
            'product'              => 'المنتج',
            'product-placeholder'  => 'كل المنتجات',
            'category'             => 'فئة المنتج',
            'category-placeholder' => 'كل الفئات',
            'storage-category'     => 'فئة التخزين',
            'out-location'         => 'تخزين في',
            'sub-location'         => 'الموقع الفرعي',
            'company'              => 'الشركة',
        ],
    ],

    'table' => [
        'columns' => [
            'in-location'      => 'عند وصول المنتج إلى',
            'product'          => 'المنتج',
            'category'         => 'فئة المنتج',
            'storage-category' => 'فئة التخزين',
            'out-location'     => 'تخزين في',
            'sub-location'     => 'الموقع الفرعي',
            'company'          => 'الشركة',
            'deleted-at'       => 'تاريخ الحذف',
            'created-at'       => 'تاريخ الإنشاء',
            'updated-at'       => 'تاريخ التحديث',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'تم تحديث قاعدة التخزين',
                    'body'  => 'تم تحديث قاعدة التخزين بنجاح.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'تمت استعادة قاعدة التخزين',
                    'body'  => 'تمت استعادة قاعدة التخزين بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف قاعدة التخزين',
                    'body'  => 'تم حذف قاعدة التخزين بنجاح.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'error' => [
                        'title' => 'تعذّر حذف قاعدة التخزين',
                        'body'  => 'لا يمكن حذف قاعدة التخزين نهائياً لأنها مرتبطة بسجلات أخرى.',
                    ],

                    'success' => [
                        'title' => 'تم الحذف النهائي لقاعدة التخزين',
                        'body'  => 'تم حذف قاعدة التخزين نهائياً بنجاح.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'تمت استعادة قواعد التخزين',
                    'body'  => 'تمت استعادة قواعد التخزين بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف قواعد التخزين',
                    'body'  => 'تم حذف قواعد التخزين بنجاح.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'error' => [
                        'title' => 'تعذّر حذف قواعد التخزين',
                        'body'  => 'بعض قواعد التخزين لا يمكن حذفها نهائياً لأنها مرتبطة بسجلات أخرى.',
                    ],

                    'success' => [
                        'title' => 'تم الحذف النهائي لقواعد التخزين',
                        'body'  => 'تم حذف قواعد التخزين نهائياً بنجاح.',
                    ],
                ],
            ],
        ],
    ],
];
