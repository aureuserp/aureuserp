<?php

return [
    'title'        => 'مستودع أصول',
    'plural-title' => 'مستودعات الأصول',

    'navigation' => [
        'title' => 'مستودعات الأصول',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'بيانات المستودع',
                'fields' => [
                    'name'             => 'الاسم',
                    'code'             => 'الكود',
                    'code-help'        => 'كود داخلي اختياري، فريد على مستوى الشركة.',
                    'note'             => 'العنوان / الموقع والملاحظات',
                    'note-placeholder' => 'ملاحظات الموقع أو الحفظ.',
                ],
            ],

            'settings' => [
                'title'  => 'الإعدادات',
                'fields' => [
                    'company'        => 'الشركة',
                    'custodian'      => 'أمين المستودع',
                    'custodian-help' => 'الموظف المسؤول عن الأصول المحفوظة في هذا المستودع.',
                    'is-active'      => 'نشط',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'         => 'الاسم',
            'code'         => 'الكود',
            'custodian'    => 'أمين المستودع',
            'assets-count' => 'الأصول المحفوظة',
            'total-cost'   => 'إجمالي القيمة المالية',
            'is-active'    => 'نشط',
            'company'      => 'الشركة',
            'creator'      => 'أنشئ بواسطة',
            'deleted-at'   => 'تاريخ الأرشفة',
            'created-at'   => 'تاريخ الإنشاء',
            'updated-at'   => 'تاريخ التحديث',
        ],

        'filters' => [
            'custodian' => 'أمين المستودع',
            'is-active' => 'نشط',
            'company'   => 'الشركة',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'تم تحديث المستودع',
                    'body'  => 'تم تحديث بيانات المستودع بنجاح.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'error' => [
                        'title' => 'تعذر حذف المستودع',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'   => 'بيانات المستودع',
                'entries' => [
                    'name'      => 'الاسم',
                    'code'      => 'الكود',
                    'custodian' => 'أمين المستودع',
                    'company'   => 'الشركة',
                    'is-active' => 'نشط',
                    'note'      => 'العنوان / الموقع والملاحظات',
                ],
            ],

            'audit' => [
                'title'   => 'التدقيق',
                'entries' => [
                    'creator'    => 'أنشئ بواسطة',
                    'created-at' => 'تاريخ الإنشاء',
                    'updated-at' => 'تاريخ التحديث',
                ],
            ],
        ],
    ],

    'relation-manager' => [
        'warehouse-assets' => [
            'title'   => 'الأصول المحفوظة',
            'columns' => [
                'asset-tag'     => 'وسم الأصل',
                'asset-name'    => 'اسم الأصل',
                'product'       => 'المنتج',
                'model'         => 'الموديل',
                'serial-no'     => 'الرقم التسلسلي',
                'category'      => 'التصنيف',
                'condition'     => 'الحالة',
                'cost'          => 'التكلفة',
                'purchase-date' => 'تاريخ الشراء',
            ],
            'actions' => [
                'issue' => 'صرف عهدة',
            ],
        ],
    ],

    'pages' => [
        'list' => [
            'header-actions' => [
                'create' => [
                    'label'        => 'مستودع أصول جديد',
                    'notification' => [
                        'title' => 'تم إنشاء المستودع',
                        'body'  => 'تم إنشاء مستودع الأصول بنجاح.',
                    ],
                ],
            ],
            'views' => [
                'all'      => 'جميع المستودعات',
                'archived' => 'المستودعات المؤرشفة',
            ],
        ],
        'create' => [
            'notification' => [
                'title' => 'تم إنشاء المستودع',
                'body'  => 'تم إنشاء مستودع الأصول بنجاح.',
            ],
        ],
        'edit' => [
            'notification' => [
                'title' => 'تم تحديث المستودع',
                'body'  => 'تم تحديث بيانات المستودع بنجاح.',
            ],
        ],
    ],
];
