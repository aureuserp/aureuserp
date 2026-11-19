<?php

return [
    'title'        => 'موقع أصول وعُهد',
    'plural-title' => 'مواقع الأصول والعُهد',

    'navigation' => [
        'title' => 'مواقع الأصول والعُهد',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'تفاصيل الموقع',
                'fields' => [
                    'name'              => 'الاسم',
                    'parent'            => 'الموقع الأب',
                    'usage-type'        => 'نوع الاستخدام',
                    'usage-type-help'   => 'مواقع الأصول الثابتة لتثبيت أصول الشركة؛ نطاقات العُهد لتصنيف عُهد الموظفين الشخصية.',
                    'code'              => 'الكود',
                    'code-help'         => 'كود داخلي اختياري، فريد على مستوى الشركة.',
                    'type'              => 'النوع',
                    'type-name'         => 'اسم النوع',
                    'type-icon'         => 'الأيقونة',
                    'type-color'        => 'اللون',
                    'note'              => 'العنوان / الموقع والملاحظات',
                    'note-placeholder'  => 'المبنى، الطابق، منطقة الميدان، أو تفاصيل الموقع الأخرى.',
                ],
            ],

            'settings' => [
                'title'  => 'الإعدادات',
                'fields' => [
                    'company'                  => 'الشركة',
                    'default-custodian'        => 'أمين الموقع الافتراضي',
                    'default-custodian-help'   => 'الموظف الذي يتم تعيينه تلقائيًا كمستلم للعهدة عند إرسال الأصول إلى هذا الموقع.',
                    'is-active'                => 'نشط',
                    'requires-custodian'       => 'يتطلب أمين عهدة',
                    'requires-custodian-help'  => 'تتطلب المواقع الميدانية ومواقع المركبات مستلمًا محددًا لكل تسليم عهدة.',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'               => 'الاسم',
            'full-name'          => 'الاسم الكامل',
            'usage-type'         => 'نوع الاستخدام',
            'code'               => 'الكود',
            'type'               => 'النوع',
            'default-custodian'  => 'أمين العهدة الافتراضي',
            'is-active'          => 'نشط',
            'requires-custodian' => 'يتطلب أمين عهدة',
            'assets-count'       => 'الأصول',
            'total-cost'         => 'إجمالي التقييم',
            'company'            => 'الشركة',
            'creator'            => 'أنشئ بواسطة',
            'deleted-at'         => 'تاريخ الأرشفة',
            'created-at'         => 'تاريخ الإنشاء',
            'updated-at'         => 'تاريخ التحديث',
        ],

        'filters' => [
            'usage-type'         => 'نوع الاستخدام',
            'parent'             => 'الموقع الأب',
            'type'               => 'النوع',
            'is-active'          => 'نشط',
            'requires-custodian' => 'يتطلب أمين عهدة',
            'company'            => 'الشركة',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'تم تحديث الموقع',
                    'body'  => 'تم تحديث الموقع بنجاح.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'error' => [
                        'title' => 'تعذر حذف الموقع',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'   => 'تفاصيل الموقع',
                'entries' => [
                    'name'               => 'الاسم',
                    'full-name'          => 'الاسم الكامل',
                    'parent'             => 'الموقع الأب',
                    'usage-type'         => 'نوع الاستخدام',
                    'code'               => 'الكود',
                    'type'               => 'النوع',
                    'company'            => 'الشركة',
                    'is-active'          => 'نشط',
                    'requires-custodian' => 'يتطلب أمين عهدة',
                    'default-custodian'  => 'أمين العهدة الافتراضي',
                    'note'               => 'العنوان / الموقع والملاحظات',
                ],
            ],

            'audit' => [
                'title'   => 'سجل التدقيق',
                'entries' => [
                    'creator'    => 'أنشئ بواسطة',
                    'created-at' => 'تاريخ الإنشاء',
                    'updated-at' => 'تاريخ التحديث',
                ],
            ],
        ],
    ],

    'relation-manager' => [
        'site-assets' => [
            'title'   => 'محتويات الموقع',
            'columns' => [
                'asset-tag'          => 'رقم الأصل',
                'asset-name'         => 'الأصل',
                'category'           => 'الفئة',
                'current-site'       => 'الموقع / الموقع الفرعي',
                'serial-no'          => 'الرقم التسلسلي',
                'receiver'           => 'المستلم بواسطة',
                'condition-out'      => 'الحالة عند الصرف',
                'condition-in'       => 'الحالة عند الاستلام',
                'cost'               => 'التكلفة',
                'issued-at'          => 'تاريخ الصرف',
                'expected-return-at' => 'تاريخ الإرجاع المتوقع',
                'status'             => 'الحالة',
                'ended-type'         => 'انتهت كـ',
                'issued-by'          => 'صرف بواسطة',
            ],
            'filters' => [
                'status' => 'الحالة',
            ],
            'status' => [
                'open'   => 'في الموقع',
                'closed' => 'تم الإرجاع',
            ],
            'tabs' => [
                'all'           => 'الكل (شامل الفروع)',
                'direct'        => 'الأصول المباشرة',
                'subsites'      => 'المواقع التابعة',
                'with-children' => ':site (شامل الفروع)',
                'only'          => ':site (مباشر فقط)',
            ],
        ],
    ],

    'pages' => [
        'list' => [
            'header-actions' => [
                'create' => [
                    'label'        => 'موقع جديد',
                    'notification' => [
                        'title' => 'تم إنشاء الموقع',
                        'body'  => 'تم إنشاء الموقع بنجاح.',
                    ],
                ],
            ],
            'views' => [
                'all'             => 'جميع المواقع',
                'asset-locations' => 'مواقع أصول ثابتة',
                'custody-zones'   => 'نطاقات عُهد',
                'archived'        => 'المواقع المؤرشفة',
            ],
        ],
        'create' => [
            'notification' => [
                'title' => 'تم إنشاء الموقع',
                'body'  => 'تم إنشاء الموقع بنجاح.',
            ],
        ],
        'edit' => [
            'notification' => [
                'title' => 'تم تحديث الموقع',
                'body'  => 'تم تحديث الموقع بنجاح.',
            ],
        ],
    ],
];
