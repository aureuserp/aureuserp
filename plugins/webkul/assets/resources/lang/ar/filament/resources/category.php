<?php

return [
    'title'        => 'فئة أصل',
    'plural-title' => 'فئات الأصول',

    'navigation' => [
        'title' => 'فئات الأصول',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'تفاصيل الفئة',
                'fields' => [
                    'name'   => 'اسم الفئة',
                    'parent' => 'الفئة الرئيسية',
                ],
            ],

            'settings' => [
                'title'  => 'الإعدادات',
                'fields' => [
                    'company' => 'الشركة',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'           => 'الاسم',
            'full-name'      => 'المسار الكامل',
            'parent'         => 'الفئة الرئيسية',
            'products-count' => 'المنتجات',
            'assets-count'   => 'الأصول',
            'total-cost'     => 'إجمالي القيمة',
            'company'        => 'الشركة',
            'creator'        => 'أنشئ بواسطة',
            'created-at'     => 'تاريخ الإنشاء',
            'updated-at'     => 'تاريخ التحديث',
        ],

        'filters' => [
            'parent'  => 'الفئة الرئيسية',
            'company' => 'الشركة',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'تم تحديث الفئة',
                    'body'  => 'تم تحديث الفئة بنجاح.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'   => 'تفاصيل الفئة',
                'entries' => [
                    'name'           => 'الاسم',
                    'full-name'      => 'المسار الكامل',
                    'parent'         => 'الفئة الرئيسية',
                    'products-count' => 'المنتجات',
                    'assets-count'   => 'الأصول',
                    'total-cost'     => 'إجمالي القيمة',
                ],
            ],

            'audit' => [
                'title'   => 'سجل التدقيق',
                'entries' => [
                    'creator'    => 'أنشئ بواسطة',
                    'created-at' => 'تاريخ الإنشاء',
                    'updated-at' => 'تاريخ التحديث',
                ],
            ],
        ],
    ],

    'relation-manager' => [
        'category-products' => [
            'title' => 'المنتجات ضمن هذه الفئة',
        ],
    ],

    'pages' => [
        'list' => [
            'header-actions' => [
                'create' => [
                    'label'        => 'فئة جديدة',
                    'notification' => [
                        'title' => 'تم إنشاء الفئة',
                        'body'  => 'تم إنشاء الفئة بنجاح.',
                    ],
                ],
            ],
        ],
        'create' => [
            'notification' => [
                'title' => 'تم إنشاء الفئة',
                'body'  => 'تم إنشاء الفئة بنجاح.',
            ],
        ],
        'edit' => [
            'notification' => [
                'title' => 'تم تحديث الفئة',
                'body'  => 'تم تحديث الفئة بنجاح.',
            ],
        ],
    ],
];
