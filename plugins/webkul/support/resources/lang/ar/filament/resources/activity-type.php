<?php

return [
    'title' => 'الأقسام',

    'navigation' => [
        'title' => 'الأقسام',
        'group' => 'الموظفون',
    ],

    'form' => [
        'sections' => [
            'activity-type-details' => [
                'title' => 'معلومات عامة',

                'fields' => [
                    'name'                => 'نوع النشاط',
                    'name-tooltip'        => 'أدخل اسم نوع النشاط الرسمي',
                    'action'              => 'الإجراء',
                    'default-user'        => 'المستخدم الافتراضي',
                    'summary'             => 'الملخص',
                    'note'                => 'ملاحظة',
                ],
            ],

            'delay-information' => [
                'title' => 'معلومات التأخير',

                'fields' => [
                    'delay-count'            => 'عدد التأخير',
                    'delay-unit'             => 'وحدة التأخير',
                    'delay-form'             => 'نموذج التأخير',
                    'delay-form-helper-text' => 'مصدر حساب التأخير',
                ],
            ],

            'advanced-information' => [
                'title' => 'معلومات متقدمة',

                'fields' => [
                    'icon'                => 'الأيقونة',
                    'decoration-type'     => 'نوع الزخرفة',
                    'chaining-type'       => 'نوع التسلسل',
                    'suggest'             => 'اقتراح',
                    'trigger'             => 'تفعيل',
                ],
            ],

            'status-and-configuration-information' => [
                'title' => 'الحالة والإعدادات',

                'fields' => [
                    'status'               => 'الحالة',
                    'keep-done-activities' => 'الاحتفاظ بالأنشطة المكتملة',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'نوع النشاط',
            'summary'    => 'الملخص',
            'planned-in' => 'مخطط في',
            'type'       => 'النوع',
            'action'     => 'الإجراء',
            'status'     => 'الحالة',
            'created-at' => 'تاريخ الإنشاء',
            'updated-at' => 'تاريخ التحديث',
        ],

        'groups' => [
            'name'             => 'الاسم',
            'action-category'  => 'فئة الإجراء',
            'status'           => 'الحالة',
            'delay-count'      => 'عدد التأخير',
            'delay-unit'       => 'وحدة التأخير',
            'delay-source'     => 'مصدر التأخير',
            'associated-model' => 'النموذج المرتبط',
            'chaining-type'    => 'نوع التسلسل',
            'decoration-type'  => 'نوع الزخرفة',
            'default-user'     => 'المستخدم الافتراضي',
            'creation-date'    => 'تاريخ الإنشاء',
            'last-update'      => 'آخر تحديث',
        ],

        'filters' => [
            'action'    => 'الإجراء',
            'status'    => 'الحالة',
            'has-delay' => 'له تأخير',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'تم استعادة نوع النشاط',
                    'body'  => 'تم استعادة نوع النشاط بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف نوع النشاط',
                    'body'  => 'تم حذف نوع النشاط بنجاح.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'تم حذف نوع النشاط نهائياً',
                        'body'  => 'تم حذف نوع النشاط نهائياً بنجاح.',
                    ],
                    'error' => [
                        'title' => 'تعذر حذف نوع النشاط',
                        'body'  => 'لا يمكن حذف نوع النشاط لأنه قيد الاستخدام حالياً.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'تم استعادة أنواع الأنشطة',
                    'body'  => 'تم استعادة أنواع الأنشطة بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف أنواع الأنشطة',
                    'body'  => 'تم حذف أنواع الأنشطة بنجاح.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'تم حذف أنواع الأنشطة نهائياً',
                    'body'  => 'تم حذف أنواع الأنشطة نهائياً بنجاح.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'activity-type-details' => [
                'title' => 'معلومات عامة',

                'entries' => [
                    'name'                => 'نوع النشاط',
                    'name-tooltip'        => 'أدخل اسم نوع النشاط الرسمي',
                    'action'              => 'الإجراء',
                    'default-user'        => 'المستخدم الافتراضي',
                    'plugin'              => 'الإضافة',
                    'summary'             => 'الملخص',
                    'note'                => 'ملاحظة',
                ],
            ],

            'delay-information' => [
                'title' => 'معلومات التأخير',

                'entries' => [
                    'delay-count'            => 'عدد التأخير',
                    'delay-unit'             => 'وحدة التأخير',
                    'delay-form'             => 'نموذج التأخير',
                    'delay-form-helper-text' => 'مصدر حساب التأخير',
                ],
            ],

            'advanced-information' => [
                'title' => 'معلومات متقدمة',

                'entries' => [
                    'icon'                => 'الأيقونة',
                    'decoration-type'     => 'نوع الزخرفة',
                    'chaining-type'       => 'نوع التسلسل',
                    'suggest'             => 'اقتراح',
                    'trigger'             => 'تفعيل',
                ],
            ],

            'status-and-configuration-information' => [
                'title' => 'الحالة والإعدادات',

                'entries' => [
                    'status'               => 'الحالة',
                    'keep-done-activities' => 'الاحتفاظ بالأنشطة المكتملة',
                ],
            ],
        ],
    ],
];
