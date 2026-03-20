<?php

return [
    'navigation' => [
        'title' => 'المستودعات',
        'group' => 'إدارة المستودعات',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'عام',
                'fields' => [
                    'name'               => 'الاسم',
                    'name-placeholder'   => 'مثال: المستودع المركزي',
                    'code'               => 'الاسم المختصر',
                    'code-placeholder'   => 'مثال: MC',
                    'code-hint-tooltip'  => 'الاسم المختصر يعمل كمعرف للمستودع.',
                    'company'            => 'الشركة',
                    'address'            => 'العنوان',
                ],
            ],

            'settings' => [
                'title'  => 'الإعدادات',

                'fields' => [
                    'shipment-management'              => 'إدارة الشحنات',
                    'incoming-shipments'               => 'الشحنات الواردة',
                    'incoming-shipments-hint-tooltip'  => 'مسار الوارد الافتراضي للاتباع',
                    'outgoing-shipments'               => 'الشحنات الصادرة',
                    'outgoing-shipments-hint-tooltip'  => 'مسار الصادر الافتراضي للاتباع',
                    'resupply-management'              => 'إدارة إعادة التوريد',
                    'resupply-management-hint-tooltip' => 'سيتم إنشاء المسارات تلقائياً لإعادة تموين هذا المستودع من المستودعات المحددة.',
                    'resupply-from'                    => 'إعادة التوريد من',
                ],
            ],

            'additional' => [
                'title'  => 'معلومات إضافية',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'الاسم',
            'code'       => 'الاسم المختصر',
            'company'    => 'الشركة',
            'address'    => 'العنوان',
            'deleted-at' => 'تاريخ الحذف',
            'created-at' => 'تاريخ الإنشاء',
            'updated-at' => 'تاريخ التحديث',
        ],

        'groups' => [
            'address'       => 'العنوان',
            'company'       => 'الشركة',
            'created-at'    => 'تاريخ الإنشاء',
            'updated-at'    => 'تاريخ التحديث',
        ],

        'filters' => [
            'company' => 'الشركة',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'تم استعادة المستودع',
                    'body'  => 'تم استعادة المستودع بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف المستودع',
                    'body'  => 'تم حذف المستودع بنجاح.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'تم حذف المستودع نهائياً',
                        'body'  => 'تم حذف المستودع نهائياً بنجاح.',
                    ],

                    'error' => [
                        'title' => 'تعذر حذف المستودع',
                        'body'  => 'لا يمكن حذف المستودع لأنه قيد الاستخدام حالياً.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'تم استعادة المستودعات',
                    'body'  => 'تم استعادة المستودعات بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف المستودعات',
                    'body'  => 'تم حذف المستودعات بنجاح.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'تم حذف المستودعات نهائياً',
                        'body'  => 'تم حذف المستودعات نهائياً بنجاح.',
                    ],

                    'error' => [
                        'title' => 'تعذر حذف المستودعات',
                        'body'  => 'لا يمكن حذف المستودعات لأنها قيد الاستخدام حالياً.',
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
                    'name'    => 'اسم المستودع',
                    'code'    => 'رمز المستودع',
                    'company' => 'الشركة',
                    'address' => 'العنوان',
                ],
            ],

            'settings' => [
                'title' => 'الإعدادات',

                'entries' => [
                    'shipment-management' => 'إدارة الشحنات',
                    'incoming-shipments'  => 'الشحنات الواردة',
                    'outgoing-shipments'  => 'الشحنات الصادرة',
                    'resupply-management' => 'إدارة إعادة التوريد',
                    'resupply-from'       => 'إعادة التوريد من',
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
