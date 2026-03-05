<?php

return [
    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'عام',

                'fields' => [
                    'name'             => 'الاسم',
                    'name-placeholder' => 'مثال: مصابيح',
                    'parent'           => 'الفئة الأم',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'        => 'الاسم',
            'full-name'   => 'الاسم الكامل',
            'parent-path' => 'مسار الفئة الأم',
            'parent'      => 'الفئة الأم',
            'creator'     => 'المنشئ',
            'created-at'  => 'تاريخ الإنشاء',
            'created-at'  => 'تاريخ الإنشاء',
            'updated-at'  => 'تاريخ التحديث',
        ],

        'groups' => [
            'parent'     => 'الفئة الأم',
            'creator'    => 'المنشئ',
            'created-at' => 'تاريخ الإنشاء',
            'updated-at' => 'تاريخ التحديث',
        ],

        'filters' => [
            'parent'  => 'الفئة الأم',
            'creator' => 'المنشئ',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'تم حذف الفئة',
                        'body'  => 'تم حذف الفئة بنجاح.',
                    ],

                    'error' => [
                        'title' => 'تعذر حذف الفئة',
                        'body'  => 'لا يمكن حذف الفئة لأنها قيد الاستخدام حالياً.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'تم حذف الفئات',
                        'body'  => 'تم حذف الفئات بنجاح.',
                    ],

                    'error' => [
                        'title' => 'تعذر حذف الفئات',
                        'body'  => 'لا يمكن حذف الفئات لأنها قيد الاستخدام حالياً.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'معلومات عامة',

                'entries' => [
                    'name'        => 'الاسم',
                    'parent'      => 'الفئة الأم',
                    'full_name'   => 'اسم الفئة الكامل',
                    'parent_path' => 'مسار الفئة',
                ],
            ],

            'record-information' => [
                'title' => 'معلومات السجل',

                'entries' => [
                    'creator'    => 'أنشئ بواسطة',
                    'created_at' => 'تاريخ الإنشاء',
                    'updated_at' => 'آخر تحديث',
                ],
            ],
        ],
    ],
];
