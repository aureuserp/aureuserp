<?php

return [
    'title' => 'العملات',

    'navigation' => [
        'title' => 'العملات',
        'group' => 'الإعدادات',
    ],

    'form' => [
        'sections' => [
            'currency-details' => [
                'title' => 'معلومات العملة',

                'fields' => [
                    'name'         => 'اسم العملة',
                    'name-tooltip' => 'أدخل اسم العملة الرسمي',
                    'symbol'       => 'رمز العملة',
                    'full-name'    => 'الاسم الكامل',
                    'iso-numeric'  => 'الرمز العددي ISO',
                ],
            ],

            'format-information' => [
                'title' => 'إعدادات التنسيق',

                'fields' => [
                    'decimal-places'        => 'عدد المنازل العشرية',
                    'rounding'              => 'دقة التقريب',
                    'rounding-helper-text'  => 'حدد دقة التقريب لحسابات العملة',
                ],
            ],

            'status-and-configuration-information' => [
                'title' => 'الحالة والإعدادات',

                'fields' => [
                    'status' => 'الحالة',
                ],
            ],

            'rates' => [
                'title'       => 'أسعار الصرف',
                'description' => 'إدارة أسعار الصرف التاريخية للعملة بالنسبة للعملة الأساسية (الدولار الأمريكي).',
                'fields' => [
                    'name'              => 'التاريخ',
                    'unit-per-currency' => 'الوحدة لكل :currency',
                    'currency-per-unit' => ':currency لكل وحدة',
                ],

                'add-rate'   => 'إضافة سعر صرف',
                'item-label' => 'سعر الصرف',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'           => 'اسم العملة',
            'symbol'         => 'الرمز',
            'full-name'      => 'الاسم الكامل',
            'iso-numeric'    => 'رمز ISO',
            'decimal-places' => 'عدد المنازل العشرية',
            'rounding'       => 'التقريب',
            'status'         => 'الحالة',
            'created-at'     => 'تاريخ الإنشاء',
            'updated-at'     => 'تاريخ التحديث',
        ],

        'groups' => [
            'name'           => 'الاسم',
            'status'         => 'الحالة',
            'decimal-places' => 'عدد المنازل العشرية',
            'creation-date'  => 'تاريخ الإنشاء',
            'last-update'    => 'آخر تحديث',
        ],

        'filters' => [
            'status' => 'الحالة',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title'   => 'تم حذف العملة',
                    'body'    => 'تم حذف العملة بنجاح.',

                    'success' => [
                        'title' => 'تم حذف العملة',
                        'body'  => 'تم حذف العملة بنجاح.',
                    ],

                    'error' => [
                        'title' => 'تعذر حذف العملة',
                        'body'  => 'لا يمكن حذف العملة لأنها قيد الاستخدام حالياً.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'تم حذف العملات',
                    'body'  => 'تم حذف العملات بنجاح.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'currency-details' => [
                'title' => 'معلومات العملة',

                'entries' => [
                    'name'         => 'اسم العملة',
                    'symbol'       => 'رمز العملة',
                    'full-name'    => 'الاسم الكامل',
                    'iso-numeric'  => 'الرمز العددي ISO',
                ],
            ],

            'format-information' => [
                'title' => 'إعدادات التنسيق',

                'entries' => [
                    'decimal-places' => 'عدد المنازل العشرية',
                    'rounding'       => 'دقة التقريب',
                ],
            ],

            'status-and-configuration-information' => [
                'title' => 'الحالة والإعدادات',

                'entries' => [
                    'status' => 'الحالة',
                ],
            ],

            'rates' => [
                'title'       => 'أسعار الصرف',

                'entries' => [
                    'name'              => 'التاريخ',
                    'unit-per-currency' => 'الوحدة لكل :currency',
                    'currency-per-unit' => ':currency لكل وحدة',
                ],
            ],
        ],
    ],
];
