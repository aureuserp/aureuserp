<?php

return [
    'model-label'        => 'عميل محتمل',
    'plural-model-label' => 'العملاء المحتملون',

    'navigation' => [
        'title' => 'العملاء المحتملون',
    ],

    'global-search' => [
        'contact' => 'جهة اتصال',
        'company' => 'الشركة',
    ],

    'form' => [
        'sections' => [
            'general' => 'العميل المحتمل',
            'sales'   => 'المبيعات',
        ],

        'tabs' => [
            'notes'      => 'الملاحظات',
        ],

        'fieldsets' => [
            'marketing' => 'التسويق',
            'analysis'  => 'التحليل',
        ],

        'fields' => [
            'name-placeholder'    => 'مثال: تسعير المنتج',
            'probability'         => 'الاحتمالية',
            'company-name'        => 'اسم الشركة',
            'address'             => 'العنوان',
            'street1'             => 'الشارع...',
            'street2'             => 'الشارع 2...',
            'city'                => 'المدينة',
            'zip'                 => 'الرمز البريدي',
            'state'               => 'الولاية',
            'country'             => 'الدولة',
            'website'             => 'الموقع الإلكتروني',
            'website-placeholder' => 'مثال: https://www.example.com',
            'salesperson'         => 'مندوب المبيعات',
            'team'                => 'فريق المبيعات',
            'contact-name'        => 'اسم جهة الاتصال',
            'email'               => 'البريد الإلكتروني',
            'job-position'        => 'المنصب الوظيفي',
            'phone'               => 'الهاتف',
            'priority'            => 'الأولوية',
            'tags'                => 'الوسوم',
            'company'             => 'الشركة',
            'campaign'            => 'الحملة',
            'medium'              => 'الوسيط',
            'source'              => 'المصدر',
            'referred-by'         => 'مُحال من',
            'assignment-date'     => 'تاريخ الإسناد',
            'closed-date'         => 'تاريخ الإغلاق',
        ],
    ],

    'infolist' => [
        'sections' => [
            'lost' => 'خاسرة',
        ],

        'entries' => [
            'lost-reason'  => 'سبب الخسارة',
            'closing-note' => 'ملاحظة الإغلاق',
        ],
    ],

    'table' => [
        'columns' => [
            'name'         => 'العميل المحتمل',
            'contact-name' => 'اسم جهة الاتصال',
            'company-name' => 'اسم الشركة',
            'email'        => 'البريد الإلكتروني',
            'phone'        => 'الهاتف',
            'city'         => 'المدينة',
            'country'      => 'الدولة',
            'salesperson'  => 'مندوب المبيعات',
            'team'         => 'فريق المبيعات',
            'priority'     => 'الأولوية',
            'tags'         => 'الوسوم',
            'source'       => 'المصدر',
            'lost-reason'  => 'سبب الخسارة',
            'created-at'   => 'تاريخ الإنشاء',
        ],
    ],

    'actions' => [
        'create' => [
            'label' => 'عميل محتمل جديد',
        ],

        'open' => [
            'label' => 'فتح',
        ],

        'schedule-activity' => [
            'label' => 'جدولة نشاط',
        ],

        'convert' => [
            'label' => 'تحويل إلى فرصة',

            'modal' => [
                'heading' => 'تحويل إلى فرصة',
                'submit'  => 'إنشاء فرصة',
            ],

            'form' => [
                'action'     => 'إجراء التحويل',
                'duplicates' => 'العملاء المحتملون والفرص المشابهة للدمج',

                'customer'          => 'العميل',
                'existing-customer' => 'عميل حالي',
                'salesperson'       => 'مندوب المبيعات',
                'team'              => 'فريق المبيعات',
            ],

            'notification' => [
                'title' => 'تم التحويل إلى فرصة',
                'body'  => 'أصبح ":name" الآن فرصة في مسار المبيعات.',

                'invalid' => [
                    'title' => 'اختيار غير صالح',
                    'body'  => 'العميل أو مندوب المبيعات أو فريق المبيعات المحدد لا ينتمي إلى شركة هذا العميل المحتمل.',
                ],
            ],
        ],

        'mass-convert' => [
            'label' => 'تحويل إلى فرص',

            'modal' => [
                'heading' => 'تحويل إلى فرص',
                'submit'  => 'تحويل',
            ],

            'form' => [
                'deduplicate'             => 'تطبيق إزالة التكرار',
                'deduplicate-helper'      => 'دمج كل عميل محتمل مع العملاء المحتملين والفرص المشابهة (نفس البريد أو الهاتف أو العميل).',
                'salespeople'             => 'مندوبو المبيعات',
                'salespeople-helper'      => 'يتم توزيع العملاء المحتملين بدون مندوب عليهم بالتناوب.',
                'force-assignment'        => 'فرض التعيين',
                'force-assignment-helper' => 'إعادة تعيين العملاء المحتملين الذين لديهم مندوب أيضًا.',

                'customer-options' => [
                    'create' => 'الربط بعميل حالي أو إنشاء عميل',
                    'link'   => 'الربط بعميل حالي',
                ],
            ],

            'notification' => [
                'title' => 'تم تحويل العملاء المحتملين',
                'body'  => 'تم تحويل :count عميل محتمل، ودمج :merged سجل مشابه، وتخطي :skipped.',
            ],
        ],
        'lost' => [
            'notification' => [
                'title' => 'تمت خسارة العميل المحتمل',
                'body'  => 'تم تحديد العميل المحتمل كخاسر ونقله إلى عرض الخاسرة.',
            ],
        ],

        'restore' => [
            'notification' => [
                'title' => 'تم استعادة العميل المحتمل',
                'body'  => 'تم استعادة العميل المحتمل.',
            ],
        ],

        'force-delete' => [
            'notification' => [
                'title' => 'تم حذف العميل المحتمل',
                'body'  => 'تم حذف العميل المحتمل نهائياً.',
            ],
        ],
    ],
];
