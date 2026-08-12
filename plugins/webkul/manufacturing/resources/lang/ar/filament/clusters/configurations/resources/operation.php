<?php

return [
    'navigation' => [
        'title' => 'العمليات',
        'group' => 'الإعدادات',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'عام',
                'fields' => [
                    'name'              => 'العملية',
                    'name-placeholder'  => 'مثال: قطع',
                    'bill-of-material'  => 'قائمة المواد',
                    'work-center'       => 'مركز العمل',
                    'apply-on-variants' => 'تطبيق على المتغيرات',
                    'company'           => 'الشركة',
                    'blocked-by'        => 'محظور بواسطة',
                ],
            ],
            'settings' => [
                'title'  => 'الإعدادات',
                'fields' => [
                    'time-mode'                  => 'احتساب المدة',
                    'time-mode-batch'            => 'بناءً على',
                    'time-mode-batch-prefix'     => 'آخر',
                    'time-mode-batch-suffix'     => 'أوامر عمل',
                    'manual-cycle-time'          => 'المدة الافتراضية',
                    'manual-cycle-time-suffix'   => 'دقائق',
                ],
            ],
            'worksheet' => [
                'title'  => 'ورقة العمل',
                'fields' => [
                    'worksheet'                => 'ورقة العمل',
                    'pdf'                      => 'PDF',
                    'google-slide'             => 'شرائح Google',
                    'google-slide-placeholder' => 'رابط شرائح Google',
                    'description'              => 'الوصف',
                    'description-placeholder'  => 'وصف العملية...',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'              => 'العملية',
            'bill-of-material'  => 'قائمة المواد',
            'work-center'       => 'مركز العمل',
            'time-mode'         => 'احتساب المدة',
            'manual-cycle-time' => 'المدة الافتراضية',
            'worksheet-type'    => 'ورقة العمل',
            'deleted-at'        => 'تاريخ الحذف',
            'created-at'        => 'تاريخ الإنشاء',
            'updated-at'        => 'تاريخ التحديث',
        ],
        'filters' => [
            'work-center'    => 'مركز العمل',
            'time-mode'      => 'احتساب المدة',
            'worksheet-type' => 'ورقة العمل',
        ],
        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'تمت استعادة العملية',
                    'body'  => 'تمت استعادة العملية بنجاح.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'تمت أرشفة العملية',
                    'body'  => 'تمت أرشفة العملية بنجاح.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'تم حذف العملية',
                        'body'  => 'تم حذف العملية نهائياً.',
                    ],
                    'error' => [
                        'title' => 'تعذر حذف العملية',
                        'body'  => 'لا يمكن حذف العملية لأنها مستخدمة حالياً.',
                    ],
                ],
            ],
        ],
        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'تمت استعادة العمليات',
                    'body'  => 'تمت استعادة العمليات المحددة بنجاح.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'تمت أرشفة العمليات',
                    'body'  => 'تمت أرشفة العمليات المحددة بنجاح.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'تم حذف العمليات',
                        'body'  => 'تم حذف العمليات المحددة نهائياً.',
                    ],
                    'error' => [
                        'title' => 'تعذر حذف العمليات',
                        'body'  => 'واحدة أو أكثر من العمليات المحددة مستخدمة حالياً.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'   => 'المعلومات العامة',
                'entries' => [
                    'name'              => 'العملية',
                    'bill-of-material'  => 'قائمة المواد',
                    'work-center'       => 'مركز العمل',
                    'apply-on-variants' => 'تطبيق على المتغيرات',
                    'company'           => 'الشركة',
                ],
            ],
            'settings' => [
                'title'   => 'الإعدادات',
                'entries' => [
                    'time-mode'                => 'احتساب المدة',
                    'time-mode-batch'          => 'بناءً على',
                    'manual-cycle-time'        => 'المدة الافتراضية',
                    'manual-cycle-time-suffix' => 'دقائق',
                ],
            ],
            'worksheet' => [
                'title'   => 'ورقة العمل',
                'entries' => [
                    'worksheet'    => 'ورقة العمل',
                    'pdf'          => 'PDF',
                    'google-slide' => 'شرائح Google',
                    'description'  => 'الوصف',
                ],
            ],
            'record-information' => [
                'title'   => 'معلومات السجل',
                'entries' => [
                    'created-by'   => 'أنشئ بواسطة',
                    'created-at'   => 'تاريخ الإنشاء',
                    'last-updated' => 'آخر تحديث',
                ],
            ],
        ],
    ],
];
