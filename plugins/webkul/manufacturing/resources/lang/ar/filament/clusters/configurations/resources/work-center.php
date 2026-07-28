<?php

return [
    'navigation' => [
        'title' => 'مراكز العمل',
        'group' => 'الإعداد',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'عام',
                'fields' => [
                    'name'                     => 'الاسم',
                    'name-placeholder'         => 'مثال: خط التجميع 1',
                    'code'                     => 'الرمز',
                    'code-placeholder'         => 'مثال: AL1',
                    'working-state'            => 'حالة العمل',
                    'color'                    => 'اللون',
                    'tags'                     => 'الوسم',
                    'alternative-work-centers' => 'مراكز العمل البديلة',
                    'company'                  => 'الشركة',
                    'calendar'                 => 'ساعات العمل',
                ],
            ],

            'information' => [
                'title'     => 'معلومات عامة',
                'fieldsets' => [
                    'production-information' => 'معلومات الإنتاج',
                    'costing-information'    => 'معلومات التكلفة',
                ],
                'fields' => [
                    'default-capacity' => 'السعة الافتراضية',
                    'time-efficiency'  => 'كفاءة الوقت',
                    'oee-target'       => 'هدف OEE',
                    'costs-per-hour'   => 'التكلفة في الساعة',
                    'cost-suffix'      => 'في الساعة',
                    'setup-time'       => 'وقت الإعداد',
                    'cleanup-time'     => 'وقت التنظيف',
                    'time-suffix'      => 'دقائق',
                ],
            ],

            'description' => [
                'title'  => 'الوصف',
                'fields' => [
                    'note'             => 'الوصف',
                    'note-placeholder' => 'وصف مركز العمل...',
                ],
            ],

            'specific-capacity' => [
                'title'  => 'السعة الخاصة',
                'fields' => [
                    'records' => 'السعة الخاصة',
                ],
                'columns' => [
                    'product'      => 'المنتج',
                    'product-uom'  => 'وحدة القياس',
                    'capacity'     => 'الطاقة',
                    'setup-time'   => 'وقت الإعداد',
                    'cleanup-time' => 'وقت التنظيف',
                ],
                'actions' => [
                    'add' => 'إضافة سطر',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'             => 'الاسم',
            'code'             => 'الرمز',
            'company'          => 'الشركة',
            'calendar'         => 'ساعات العمل',
            'working-state'    => 'حالة العمل',
            'default-capacity' => 'الطاقة',
            'time-efficiency'  => 'الكفاءة',
            'costs-per-hour'   => 'التكلفة في الساعة',
            'deleted-at'       => 'تاريخ الحذف',
            'created-at'       => 'تاريخ الإنشاء',
            'updated-at'       => 'تاريخ التحديث',
        ],

        'groups' => [
            'company' => 'الشركة',
        ],

        'filters' => [
            'company'       => 'الشركة',
            'working-state' => 'حالة العمل',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'تمت استعادة مركز العمل',
                    'body'  => 'تمت استعادة مركز العمل بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تمت أرشفة مركز العمل',
                    'body'  => 'تمت أرشفة مركز العمل بنجاح.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'تم حذف مركز العمل',
                        'body'  => 'تم حذف مركز العمل نهائياً.',
                    ],

                    'error' => [
                        'title' => 'تعذر حذف مركز العمل',
                        'body'  => 'لا يمكن حذف مركز العمل لأنه مستخدم حالياً.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'تمت استعادة مراكز العمل',
                    'body'  => 'تمت استعادة مراكز العمل المحددة بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تمت أرشفة مراكز العمل',
                    'body'  => 'تمت أرشفة مراكز العمل المحددة بنجاح.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'تم حذف مراكز العمل',
                        'body'  => 'تم حذف مراكز العمل المحددة نهائياً.',
                    ],

                    'error' => [
                        'title' => 'تعذر حذف مراكز العمل',
                        'body'  => 'واحد أو أكثر من مراكز العمل المحددة مستخدمة حالياً.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'المعلومات العامة',

                'entries' => [
                    'name'                     => 'اسم مركز العمل',
                    'code'                     => 'الرمز',
                    'working-state'            => 'حالة العمل',
                    'tags'                     => 'الوسم',
                    'alternative-work-centers' => 'مراكز العمل البديلة',
                    'company'                  => 'الشركة',
                    'calendar'                 => 'ساعات العمل',
                ],
            ],

            'information' => [
                'title'     => 'معلومات عامة',
                'fieldsets' => [
                    'production-information' => 'معلومات الإنتاج',
                    'costing-information'    => 'معلومات التكلفة',
                ],

                'entries' => [
                    'default-capacity' => 'السعة الافتراضية',
                    'time-efficiency'  => 'كفاءة الوقت',
                    'oee-target'       => 'هدف OEE',
                    'costs-per-hour'   => 'التكلفة في الساعة',
                    'cost-suffix'      => 'لكل مركز عمل',
                    'setup-time'       => 'وقت الإعداد',
                    'cleanup-time'     => 'وقت التنظيف',
                    'time-suffix'      => 'دقائق',
                ],
            ],

            'description' => [
                'title'   => 'الوصف',
                'entries' => [
                    'note' => 'الوصف',
                ],
            ],

            'specific-capacity' => [
                'title'   => 'القدرات الخاصة',
                'columns' => [
                    'product'      => 'المنتج',
                    'product-uom'  => 'وحدة القياس',
                    'capacity'     => 'الطاقة',
                    'setup-time'   => 'وقت الإعداد',
                    'cleanup-time' => 'وقت التنظيف',
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
