<?php

return [
    'navigation' => [
        'title' => 'المشاريع',
        'group' => 'المشروع',
    ],

    'global-search' => [
        'project-manager' => 'مدير المشروع',
        'customer'        => 'العميل',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'عام',

                'fields' => [
                    'name'             => 'الاسم',
                    'name-placeholder' => 'اسم المشروع...',
                    'description'      => 'الوصف',
                ],
            ],

            'additional' => [
                'title' => 'معلومات إضافية',

                'fields' => [
                    'project-manager'             => 'مدير المشروع',
                    'customer'                    => 'العميل',
                    'start-date'                  => 'تاريخ البداية',
                    'end-date'                    => 'تاريخ النهاية',
                    'allocated-hours'             => 'الساعات المخصصة',
                    'allocated-hours-helper-text' => 'بالساعات (مثال: 1.5 ساعة تعني ساعة و30 دقيقة)',
                    'tags'                        => 'الوسوم',
                    'company'                     => 'الشركة',
                ],
            ],

            'settings' => [
                'title' => 'الإعدادات',

                'fields' => [
                    'visibility'                   => 'الرؤية',
                    'visibility-hint-tooltip'      => 'اسمح للموظفين بالوصول إلى مشروعك أو مهامك عن طريق إضافتهم كمتابعين. سيحصلون تلقائياً على حق الوصول لأي مهام مسندة إليهم.',
                    'private-description'          => 'المستخدمون الداخليون المدعوون فقط.',
                    'internal-description'         => 'جميع المستخدمين الداخليين يمكنهم الرؤية.',
                    'public-description'           => 'مستخدمو البوابة المدعوون وجميع المستخدمين الداخليين.',
                    'time-management'              => 'إدارة الوقت',
                    'allow-timesheets'             => 'السماح بجداول الوقت',
                    'allow-timesheets-helper-text' => 'تسجيل الوقت على المهام وتتبع التقدم',
                    'task-management'              => 'إدارة المهام',
                    'allow-milestones'             => 'السماح بالمراحل الرئيسية',
                    'allow-milestones-helper-text' => 'مراقبة المراحل الرئيسية الضرورية لتحقيق النجاح.',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'            => 'الاسم',
            'customer'        => 'العميل',
            'start-date'      => 'تاريخ البداية',
            'end-date'        => 'تاريخ النهاية',
            'planned-date'    => 'التاريخ المخطط',
            'remaining-hours' => 'الساعات المتبقية',
            'project-manager' => 'مدير المشروع',
        ],

        'groups' => [
            'stage'           => 'المرحلة',
            'project-manager' => 'مدير المشروع',
            'customer'        => 'العميل',
            'created-at'      => 'تاريخ الإنشاء',
        ],

        'filters' => [
            'name'             => 'الاسم',
            'visibility'       => 'الرؤية',
            'start-date'       => 'تاريخ البداية',
            'end-date'         => 'تاريخ النهاية',
            'allow-timesheets' => 'السماح بجداول الوقت',
            'allow-milestones' => 'السماح بالمراحل الرئيسية',
            'allocated-hours'  => 'الساعات المخصصة',
            'created-at'       => 'تاريخ الإنشاء',
            'updated-at'       => 'تاريخ التحديث',
            'stage'            => 'المرحلة',
            'customer'         => 'العميل',
            'project-manager'  => 'مدير المشروع',
            'company'          => 'الشركة',
            'creator'          => 'المُنشئ',
            'tags'             => 'الوسوم',
        ],

        'actions' => [
            'tasks'      => ':count مهام',
            'milestones' => ':completed مراحل رئيسية مكتملة من أصل :all',

            'restore' => [
                'notification' => [
                    'title' => 'تم استعادة المشروع',
                    'body'  => 'تم استعادة المشروع بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف المشروع',
                    'body'  => 'تم حذف المشروع بنجاح.',
                ],
            ],

            'force-delete' => [

                'notification' => [

                    'success' => [
                        'title' => 'تم حذف المشروع نهائياً',
                        'body'  => 'تم حذف المشروع نهائياً بنجاح.',
                    ],

                    'error' => [
                        'title' => 'لا يمكن حذف المشروع نهائياً',
                        'body'  => 'المشروع مرتبط بسجلات أخرى.',
                    ],

                ],
            ],

        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'عام',

                'entries' => [
                    'name'             => 'الاسم',
                    'name-placeholder' => 'اسم المشروع...',
                    'description'      => 'الوصف',
                ],
            ],

            'additional' => [
                'title' => 'معلومات إضافية',

                'entries' => [
                    'project-manager'        => 'مدير المشروع',
                    'customer'               => 'العميل',
                    'project-timeline'       => 'الجدول الزمني للمشروع',
                    'allocated-hours'        => 'الساعات المخصصة',
                    'allocated-hours-suffix' => ' ساعة',
                    'remaining-hours'        => 'الساعات المتبقية',
                    'remaining-hours-suffix' => ' ساعة',
                    'current-stage'          => 'المرحلة الحالية',
                    'tags'                   => 'الوسوم',
                ],
            ],

            'statistics' => [
                'title' => 'الإحصائيات',

                'entries' => [
                    'total-tasks'         => 'إجمالي المهام',
                    'milestones-progress' => 'تقدم المراحل الرئيسية',
                ],
            ],

            'record-information' => [
                'title' => 'معلومات السجل',

                'entries' => [
                    'created-at'   => 'تاريخ الإنشاء',
                    'created-by'   => 'أنشئ بواسطة',
                    'last-updated' => 'آخر تحديث',
                ],
            ],

            'settings' => [
                'title' => 'إعدادات المشروع',

                'entries' => [
                    'visibility'         => 'الرؤية',
                    'timesheets-enabled' => 'جداول الوقت مفعلة',
                    'milestones-enabled' => 'المراحل الرئيسية مفعلة',
                ],
            ],
        ],
    ],
];
