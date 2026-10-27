<?php

return [
    'model-label'        => 'الفرصة',
    'plural-model-label' => 'الفرص',

    'views' => [
        'kanban' => 'كانبان',
        'list'   => 'قائمة',
        'graph'  => 'رسم بياني',
    ],

    'navigation' => [
        'title' => 'مسار مبيعاتي',
    ],

    'global-search' => [
        'customer' => 'العميل',
        'stage'    => 'المرحلة',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'الفرصة',

                'fields' => [
                    'name'             => 'الفرصة',
                    'name-placeholder' => 'مثال: تسعير المنتج',
                    'expected-revenue' => 'الإيرادات المتوقعة',
                    'probability'      => 'الاحتمالية',
                    'customer'         => 'العميل',
                    'email'            => 'البريد الإلكتروني',
                    'phone'            => 'الهاتف',
                ],
            ],

            'notes' => [
                'title' => 'ملاحظات داخلية',
            ],

            'sales' => [
                'title' => 'المبيعات',

                'fields' => [
                    'salesperson'      => 'مندوب المبيعات',
                    'team'             => 'فريق المبيعات',
                    'company'          => 'الشركة',
                    'expected-closing' => 'الإغلاق المتوقع',
                    'priority'         => 'الأولوية',
                    'tags'             => 'الوسوم',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'lost' => [
                'title' => 'خاسرة',

                'entries' => [
                    'lost-reason' => 'سبب الخسارة',
                    'feedback'    => 'ملاحظة الإغلاق',
                ],
            ],

            'general' => [
                'title' => 'الفرصة',

                'entries' => [
                    'expected-revenue' => 'الإيرادات المتوقعة',
                    'probability'      => 'الاحتمالية',
                    'customer'         => 'العميل',
                    'email'            => 'البريد الإلكتروني',
                    'phone'            => 'الهاتف',
                ],
            ],

            'notes' => [
                'title' => 'ملاحظات داخلية',
            ],

            'sales' => [
                'title' => 'المبيعات',

                'entries' => [
                    'salesperson'      => 'مندوب المبيعات',
                    'team'             => 'فريق المبيعات',
                    'company'          => 'الشركة',
                    'expected-closing' => 'الإغلاق المتوقع',
                    'priority'         => 'الأولوية',
                    'tags'             => 'الوسوم',
                    'closed-at'        => 'تاريخ الإغلاق',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'             => 'الفرصة',
            'customer'         => 'العميل',
            'stage'            => 'المرحلة',
            'expected-revenue' => 'الإيرادات المتوقعة',
            'probability'      => 'الاحتمالية',
            'priority'         => 'الأولوية',
            'salesperson'      => 'مندوب المبيعات',
            'team'             => 'فريق المبيعات',
            'expected-closing' => 'الإغلاق المتوقع',
            'tags'             => 'الوسوم',
            'email'            => 'البريد الإلكتروني',
            'phone'            => 'الهاتف',
            'lost-reason'      => 'سبب الخسارة',
            'created-at'       => 'تاريخ الإنشاء',
        ],

        'summaries' => [
            'total' => 'الإجمالي',
        ],

        'groups' => [
            'stage'            => 'المرحلة',
            'team'             => 'فريق المبيعات',
            'salesperson'      => 'مندوب المبيعات',
            'priority'         => 'الأولوية',
            'expected-closing' => 'الإغلاق المتوقع',
        ],

        'actions' => [
            'schedule-activity' => [
                'label' => 'جدولة نشاط',
            ],

            'restore' => [
                'label' => 'استعادة',

                'notification' => [
                    'title' => 'تم استعادة الفرصة',
                    'body'  => 'تمت استعادة الفرصة إلى مسار المبيعات.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'تم حذف الفرصة',
                    'body'  => 'تم حذف الفرصة نهائياً.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'تم استعادة الفرص',
                    'body'  => 'تمت استعادة الفرص إلى مسار المبيعات.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'تم حذف الفرص',
                    'body'  => 'تم حذف الفرص نهائياً.',
                ],
            ],
        ],
    ],

    'actions' => [
        'new-quotation' => [
            'label'             => 'عرض سعر جديد',
            'modal-heading'     => 'عرض سعر جديد',
            'modal-description' => 'لا يوجد عميل لهذه الفرصة بعد. اختر عميل عرض السعر.',
            'submit'            => 'متابعة',
            'subheading'        => 'من الفرصة: :name',
        ],

        'won' => [
            'label' => 'مكسوبة',

            'modal' => [
                'heading'     => 'تحديد كمكسوبة',
                'description' => 'ستنتقل الفرصة إلى مرحلة الفوز باحتمالية 100%.',
            ],

            'notification' => [
                'success' => [
                    'title' => 'تم كسب الفرصة',
                    'body'  => 'تم تحديد الفرصة كمكسوبة.',
                ],

                'no-won-stage' => [
                    'title' => 'لا توجد مرحلة فوز',
                    'body'  => 'حدد مرحلة كـ "مرحلة الفوز؟" أولاً من إدارة علاقات العملاء ← الإعدادات ← المراحل.',
                ],
            ],
        ],

        'lost' => [
            'label' => 'خاسرة',

            'modal' => [
                'heading' => 'تحديد كخاسرة',
                'submit'  => 'تحديد كخاسرة',
            ],

            'form' => [
                'lost-reason' => 'سبب الخسارة',
                'feedback'    => 'ملاحظة الإغلاق',
            ],

            'notification' => [
                'title' => 'تمت خسارة الفرصة',
                'body'  => 'تم تحديد الفرصة كخاسرة ونقلها إلى عرض الخاسرة.',
            ],
        ],
    ],

    'quotations' => [
        'title' => 'عروض الأسعار',

        'columns' => [
            'number'      => 'الرقم',
            'date'        => 'التاريخ',
            'customer'    => 'العميل',
            'salesperson' => 'مندوب المبيعات',
            'total'       => 'الإجمالي',
            'status'      => 'الحالة',
        ],

        'empty' => [
            'heading'     => 'لا توجد عروض أسعار بعد',
            'description' => 'استخدم "عرض سعر جديد" لإنشاء عرض سعر من هذه الفرصة.',
        ],
    ],
];
