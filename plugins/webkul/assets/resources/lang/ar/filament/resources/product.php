<?php

return [
    'navigation' => [
        'title' => 'المنتجات والموديلات',
    ],

    'label'        => 'منتج أصل',
    'plural-label' => 'منتجات الأصول',

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'البيانات العامة',
                'fields' => [
                    'name'                 => 'اسم المنتج',
                    'category'             => 'الفئة',
                    'type'                 => 'نوع المنتج',
                    'type-asset-hint'      => 'وحدة متتبعة تُعرّف بالرقم التسلسلي والعهدة.',
                    'type-consumable-hint' => 'دفعة كمية تُصرف بالوحدات دون تتبع الإرجاع.',
                    'model'                => 'الموديل / الطراز',
                    'cost'                 => 'التكلفة الافتراضية',
                    'is-active'            => 'نشط',
                    'note'                 => 'المواصفات المشتركة / الملاحظات',
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

    'infolist' => [
        'sections' => [
            'general' => [
                'title'   => 'البيانات العامة',
                'entries' => [
                    'name'      => 'اسم المنتج',
                    'category'  => 'الفئة',
                    'type'      => 'نوع المنتج',
                    'model'     => 'الموديل / الطراز',
                    'cost'      => 'التكلفة الافتراضية',
                    'is-active' => 'نشط',
                    'note'      => 'المواصفات المشتركة / الملاحظات',
                ],
            ],

            'settings' => [
                'title'   => 'الإعدادات',
                'entries' => [
                    'company' => 'الشركة',
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

    'table' => [
        'columns' => [
            'name'                    => 'اسم المنتج',
            'category'                => 'الفئة',
            'type'                    => 'نوع المنتج',
            'model'                   => 'الموديل',
            'cost'                    => 'التكلفة',
            'assets-count'            => 'الوحدات المسجلة',
            'available-assets-count'  => 'المتاح بالمخازن',
            'held-assets-count'       => 'المصروف كعهدة',
            'terminated-assets-count' => 'المتلف / المفقود',
            'total-cost'              => 'إجمالي القيمة المالية',
            'is-active'               => 'نشط',
            'company'                 => 'الشركة',
            'deleted-at'              => 'تاريخ الأرشفة',
            'created-at'              => 'تاريخ الإنشاء',
            'updated-at'              => 'تاريخ التحديث',
        ],

        'groups' => [
            'category' => 'الفئة',
        ],

        'filters' => [
            'category'  => 'الفئة',
            'type'      => 'نوع المنتج',
            'is-active' => 'نشط',
            'company'   => 'الشركة',
        ],
    ],

    'views' => [
        'active'   => 'المنتجات النشطة',
        'archived' => 'المنتجات المؤرشفة',
    ],

    'relation-manager' => [
        'assets' => [
            'title'   => 'وحدات الأصول المسجلة',
            'actions' => [
                'open' => 'عرض الأصل',
            ],
        ],
    ],
];
