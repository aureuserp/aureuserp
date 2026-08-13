<?php

return [
    'navigation' => [
        'group' => 'الصيانة',
        'title' => 'طلبات الصيانة',
    ],

    'form' => [
        'sections' => [
            'request' => [
                'title'  => 'الطلب',
                'fields' => [
                    'name'                      => 'الطلب',
                    'name-placeholder'          => 'مثال: الشاشة لا تعمل',
                    'equipment'                 => 'المعدة',
                    'category'                  => 'الفئة',
                    'requested-at'              => 'تاريخ الطلب',
                    'requested-at-hint-tooltip' => 'التاريخ الذي تم فيه الإبلاغ عن طلب الصيانة.',
                    'maintenance-type'          => 'نوع الصيانة',
                    'recurrent'                 => 'متكرر',
                    'repeat-every'              => 'يتكرر كل',
                    'maintenance-type-options'  => [
                        'corrective' => 'تصحيحية',
                        'preventive' => 'وقائية',
                    ],
                ],
                'tabs' => [
                    'notes' => [
                        'title'  => 'ملاحظات',
                        'fields' => [
                            'description'             => 'ملاحظات داخلية',
                            'description-placeholder' => 'ملاحظات داخلية',
                        ],
                    ],
                    'instructions' => [
                        'title'  => 'التعليمات',
                        'fields' => [
                            'instruction-type'         => 'نوع التعليمات',
                            'instruction-type-options' => [
                                'pdf'          => 'ملف PDF',
                                'google-slide' => 'عرض Google',
                                'text'         => 'نص',
                            ],
                            'instruction-pdf'              => 'ملف PDF',
                            'instruction-google-slide'     => 'عرض Google',
                            'instruction-text'             => 'الوصف',
                            'instruction-text-placeholder' => 'الوصف',
                        ],
                    ],
                ],
            ],

            'settings' => [
                'title'  => 'الإعدادات',
                'fields' => [
                    'team'                      => 'الفريق',
                    'responsible'               => 'المسؤول',
                    'scheduled-at'              => 'تاريخ الجدولة',
                    'scheduled-at-hint-tooltip' => 'تاريخ ووقت بدء أعمال الصيانة المخطط لها.',
                    'duration'                  => 'المدة',
                    'duration-hint-tooltip'     => 'مدة الصيانة المتوقعة.',
                    'duration-suffix'           => 'ساعات',
                    'priority'                  => 'الأولوية',
                    'company'                   => 'الشركة',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'الموضوع',
            'creator'    => 'أنشئ بواسطة',
            'technician' => 'الفني',
            'category'   => 'الفئة',
            'stage'      => 'المرحلة',
            'company'    => 'الشركة',
        ],

        'groups' => [
            'stage'       => 'المرحلة',
            'assigned-to' => 'مسند إلى',
            'category'    => 'الفئة',
            'created-by'  => 'أنشئ بواسطة',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'تمت استعادة طلب الصيانة',
                    'body'  => 'تمت استعادة طلب الصيانة بنجاح.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'تمت أرشفة طلب الصيانة',
                    'body'  => 'تمت أرشفة طلب الصيانة بنجاح.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'تم حذف طلب الصيانة',
                        'body'  => 'تم حذف طلب الصيانة نهائيا.',
                    ],
                    'error' => [
                        'title' => 'تعذر حذف طلب الصيانة',
                        'body'  => 'طلب الصيانة هذا مرتبط بسجل آخر.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'تمت استعادة طلبات الصيانة',
                    'body'  => 'تمت استعادة طلبات الصيانة المحددة بنجاح.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'تمت أرشفة طلبات الصيانة',
                    'body'  => 'تمت أرشفة طلبات الصيانة المحددة بنجاح.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'request' => [
                'title'   => 'الطلب',
                'entries' => [
                    'name'                     => 'الطلب',
                    'equipment'                => 'المعدة',
                    'category'                 => 'الفئة',
                    'requested-at'             => 'تاريخ الطلب',
                    'maintenance-type'         => 'نوع الصيانة',
                    'instruction-type'         => 'نوع التعليمات',
                    'instruction-pdf'          => 'ملف PDF',
                    'instruction-google-slide' => 'عرض Google',
                    'description'              => 'ملاحظات داخلية',
                    'instruction-text'         => 'الوصف',
                ],
            ],

            'settings' => [
                'title'   => 'الإعدادات',
                'entries' => [
                    'team'            => 'الفريق',
                    'responsible'     => 'المسؤول',
                    'scheduled-at'    => 'تاريخ الجدولة',
                    'duration'        => 'المدة',
                    'duration-suffix' => 'ساعات',
                    'priority'        => 'الأولوية',
                    'company'         => 'الشركة',
                ],
            ],
        ],
    ],
];
