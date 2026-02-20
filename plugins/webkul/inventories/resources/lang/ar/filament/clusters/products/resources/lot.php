<?php

return [
    'navigation' => [
        'title' => 'الدفعات / الأرقام التسلسلية',
        'group' => 'المخزون',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'عام',

                'fields' => [
                    'name'                   => 'الاسم',
                    'name-placeholder'       => 'مثال: LOT/0001/20121',
                    'product'                => 'المنتج',
                    'product-hint-tooltip'   => 'المنتج المرتبط بهذه الدفعة/الرقم التسلسلي. لا يمكن تغييره إذا تم نقله بالفعل.',
                    'reference'              => 'المرجع',
                    'reference-hint-tooltip' => 'رقم مرجع داخلي، إذا كان مختلفاً عن رقم الدفعة/التسلسلي الخاص بالشركة المصنعة.',
                    'description'            => 'الوصف',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'         => 'الاسم',
            'product'      => 'المنتج',
            'on-hand-qty'  => 'الكمية المتاحة',
            'reference'    => 'المرجع الداخلي',
            'created-at'   => 'تاريخ الإنشاء',
            'updated-at'   => 'تاريخ التحديث',
        ],

        'groups' => [
            'product'        => 'المنتج',
            'location'       => 'الموقع',
            'created-at'     => 'تاريخ الإنشاء',
        ],

        'filters' => [
            'product'  => 'المنتج',
            'location' => 'الموقع',
            'creator'  => 'المُنشئ',
            'company'  => 'الشركة',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'تم حذف الدفعة',
                        'body'  => 'تم حذف الدفعة بنجاح.',
                    ],

                    'error' => [
                        'title' => 'تعذر حذف الدفعة',
                        'body'  => 'لا يمكن حذف الدفعة لأنها قيد الاستخدام حالياً.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'print' => [
                'label' => 'طباعة الباركود',
            ],

            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'تم حذف الدفعات',
                        'body'  => 'تم حذف الدفعات بنجاح.',
                    ],

                    'error' => [
                        'title' => 'تعذر حذف الدفعات',
                        'body'  => 'لا يمكن حذف الدفعات لأنها قيد الاستخدام حالياً.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'تفاصيل الدفعة',

                'entries' => [
                    'name'        => 'اسم الدفعة',
                    'product'     => 'المنتج',
                    'reference'   => 'المرجع',
                    'description' => 'الوصف',
                    'on-hand-qty' => 'الكمية المتاحة',
                    'company'     => 'الشركة',
                    'created-at'  => 'تاريخ الإنشاء',
                    'updated-at'  => 'آخر تحديث',
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
