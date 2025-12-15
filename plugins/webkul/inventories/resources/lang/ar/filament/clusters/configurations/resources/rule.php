<?php

return [
    'navigation' => [
        'title' => 'القواعد',
        'group' => 'إدارة المستودعات',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'عام',

                'fields' => [
                    'name'                        => 'الاسم',
                    'action'                      => 'الإجراء',
                    'operation-type'              => 'نوع العملية',
                    'source-location'             => 'موقع المصدر',
                    'destination-location'        => 'موقع الوجهة',
                    'supply-method'               => 'طريقة التوريد',
                    'supply-method-hint-tooltip'  => 'الأخذ من المخزون: يتم الحصول على المنتجات مباشرة من المخزون المتاح في موقع المصدر.<br/>تفعيل قاعدة أخرى: يتجاهل النظام المخزون المتاح ويبحث عن قاعدة مخزون لتجديد موقع المصدر.<br/>الأخذ من المخزون، وإذا لم يتوفر، تفعيل قاعدة أخرى: يتم أخذ المنتجات أولاً من المخزون المتاح. إذا لم يتوفر أي منها، يطبق النظام قاعدة مخزون لجلب المنتجات إلى موقع المصدر.',
                    'automatic-move'              => 'النقل التلقائي',
                    'automatic-move-hint-tooltip' => 'عملية يدوية: ينشئ حركة مخزون منفصلة بعد الحالية.<br/>تلقائي بدون خطوة إضافية: يستبدل الموقع مباشرة في الحركة الأصلية دون إضافة خطوة إضافية.',

                    'action-information' => [
                        'pull' => 'عندما تكون المنتجات مطلوبة في <b>:sourceLocation</b>، يتم إنشاء :operation من <b>:destinationLocation</b> لتلبية الطلب.',
                        'push' => 'عندما تصل المنتجات إلى <b>:sourceLocation</b>،</br>يتم إنشاء <b>:operation</b> لنقلها إلى <b>:destinationLocation</b>.',
                        'buy'  => 'عندما تكون المنتجات مطلوبة في <b>:destinationLocation</b>، يتم إنشاء طلب عرض أسعار لتلبية الحاجة.',
                    ],
                ],
            ],

            'settings' => [
                'title'  => 'الإعدادات',

                'fields' => [
                    'partner-address'              => 'عنوان الشريك',
                    'partner-address-hint-tooltip' => 'العنوان الذي يجب تسليم البضائع إليه. اختياري.',
                    'lead-time'                    => 'المهلة الزمنية (أيام)',
                    'lead-time-hint-tooltip'       => 'سيتم حساب تاريخ النقل المتوقع باستخدام هذه المهلة الزمنية.',
                ],

                'fieldsets' => [
                    'applicability' => [
                        'title'  => 'قابلية التطبيق',

                        'fields' => [
                            'route'   => 'المسار',
                            'company' => 'الشركة',
                        ],
                    ],

                    'propagation' => [
                        'title'  => 'الانتشار',

                        'fields' => [
                            'propagation-procurement-group'              => 'انتشار مجموعة المشتريات',
                            'propagation-procurement-group-hint-tooltip' => 'إذا تم التحديد، فإن إلغاء الحركة المنشأة بواسطة هذه القاعدة سيلغي أيضاً الحركة اللاحقة.',
                            'cancel-next-move'                           => 'إلغاء الحركة التالية',
                            'warehouse-to-propagate'                     => 'المستودع للانتشار',
                            'warehouse-to-propagate-hint-tooltip'        => 'المستودع المعين للحركة أو المشتريات المنشأة، والذي قد يختلف عن المستودع الذي تنطبق عليه هذه القاعدة (مثل قواعد إعادة التوريد من مستودع آخر).',
                        ],
                    ],
                ],

            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'                 => 'الاسم',
            'action'               => 'الإجراء',
            'source-location'      => 'موقع المصدر',
            'destination-location' => 'موقع الوجهة',
            'route'                => 'المسار',
            'deleted-at'           => 'تاريخ الحذف',
            'created-at'           => 'تاريخ الإنشاء',
            'updated-at'           => 'تاريخ التحديث',
        ],

        'groups' => [
            'action'               => 'الإجراء',
            'source-location'      => 'موقع المصدر',
            'destination-location' => 'موقع الوجهة',
            'route'                => 'المسار',
            'created-at'           => 'تاريخ الإنشاء',
            'updated-at'           => 'تاريخ التحديث',
        ],

        'filters' => [
            'action'               => 'الإجراء',
            'source-location'      => 'موقع المصدر',
            'destination-location' => 'موقع الوجهة',
            'route'                => 'المسار',
            'company'              => 'الشركة',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'تم تحديث القاعدة',
                    'body'  => 'تم تحديث القاعدة بنجاح.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'تم استعادة القاعدة',
                    'body'  => 'تم استعادة القاعدة بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف القاعدة',
                    'body'  => 'تم حذف القاعدة بنجاح.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'تم حذف القاعدة نهائياً',
                        'body'  => 'تم حذف القاعدة نهائياً بنجاح.',
                    ],

                    'error' => [
                        'title' => 'تعذر حذف القاعدة',
                        'body'  => 'لا يمكن حذف القاعدة لأنها قيد الاستخدام حالياً.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'تم استعادة القواعد',
                    'body'  => 'تم استعادة القواعد بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف القواعد',
                    'body'  => 'تم حذف القواعد بنجاح.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'تم حذف القواعد نهائياً',
                        'body'  => 'تم حذف القواعد نهائياً بنجاح.',
                    ],

                    'error' => [
                        'title' => 'تعذر حذف القواعد',
                        'body'  => 'لا يمكن حذف القواعد لأنها قيد الاستخدام حالياً.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'تفاصيل القاعدة',

                'description' => [
                    'pull' => 'عندما تكون المنتجات مطلوبة في <b>:sourceLocation</b>، يتم إنشاء <b>:operation</b> من <b>:destinationLocation</b> لتلبية الطلب.',
                    'push' => 'عندما تصل المنتجات إلى <b>:sourceLocation</b>، يتم إنشاء <b>:operation</b> لنقلها إلى <b>:destinationLocation</b>.',
                ],

                'entries' => [
                    'name'                 => 'اسم القاعدة',
                    'action'               => 'الإجراء',
                    'operation-type'       => 'نوع العملية',
                    'source-location'      => 'موقع المصدر',
                    'destination-location' => 'موقع الوجهة',
                    'route'                => 'المسار',
                    'company'              => 'الشركة',
                    'partner-address'      => 'عنوان الشريك',
                    'lead-time'            => 'المهلة الزمنية',
                    'action-information'   => 'معلومات الإجراء',
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
