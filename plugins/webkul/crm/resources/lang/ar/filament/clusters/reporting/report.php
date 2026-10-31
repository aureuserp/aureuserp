<?php

return [
    'none' => 'لا شيء',

    'chart' => [
        'heading' => ':measure حسب :group',
    ],

    'filters' => [
        'measure'        => 'المقياس',
        'split-by'       => 'تقسيم حسب',
        'no-split'       => 'بدون تقسيم',
        'salespeople'    => 'مندوبو المبيعات',
        'teams'          => 'فرق المبيعات',
    ],

    'dimensions' => [
        'salesperson'         => 'مندوب المبيعات',
        'team'                => 'فريق المبيعات',
        'stage'               => 'المرحلة',
        'type'                => 'النوع',
        'city'                => 'المدينة',
        'country'             => 'الدولة',
        'lost-reason'         => 'سبب الخسارة',
        'company'             => 'الشركة',
        'campaign'            => 'الحملة',
        'medium'              => 'الوسيط',
        'source'              => 'المصدر',
        'expected-closing'    => 'الإغلاق المتوقع',
        'created-at'          => 'تاريخ الإنشاء',
        'converted-at'        => 'تاريخ التحويل',
        'closed-at'           => 'تاريخ الإغلاق',
        'activity-type'       => 'نوع النشاط',
        'assigned-to'         => 'مسند إلى',
        'done-by'             => 'أنجز بواسطة',
        'done-at'             => 'تاريخ الإنجاز',
        'activity-created-at' => 'تاريخ إنشاء النشاط',
    ],

    'measures' => [
        'count'            => 'العدد',
        'expected-revenue' => 'الإيرادات المتوقعة',
        'prorated-revenue' => 'الإيرادات المرجحة',
        'probability'      => 'الاحتمالية (المتوسط)',
        'days-to-assign'   => 'أيام الإسناد',
        'days-to-close'    => 'أيام الإغلاق',
    ],

    'presets' => [
        'my-pipeline'       => 'مسار مبيعاتي',
        'unassigned'        => 'غير مسندة',
        'opportunities'     => 'الفرص',
        'leads'             => 'العملاء المحتملون',
        'upcoming-closings' => 'الإغلاقات القادمة',
        'active'            => 'نشطة',
        'ongoing'           => 'جارية',
        'won'               => 'مكسوبة',
        'lost'              => 'خاسرة',
        'created-this-year' => 'أنشئت هذا العام',
        'closed-this-year'  => 'أغلقت هذا العام',
        'last-12-months'    => 'آخر 12 شهراً',
        'my-activities'     => 'أنشطتي',
        'done-by-me'        => 'أنجزتها أنا',
    ],

    'table' => [
        'lost' => 'خاسرة',

        'columns' => [
            'name'             => 'الاسم',
            'type'             => 'النوع',
            'customer'         => 'العميل',
            'stage'            => 'المرحلة',
            'expected-revenue' => 'الإيرادات المتوقعة',
            'probability'      => 'الاحتمالية',
            'salesperson'      => 'مندوب المبيعات',
            'team'             => 'فريق المبيعات',
            'expected-closing' => 'الإغلاق المتوقع',
            'created-at'       => 'تاريخ الإنشاء',
            'closed-at'        => 'أغلق في',
            'lost-reason'      => 'سبب الخسارة',
            'country'          => 'الدولة',
            'city'             => 'المدينة',
            'done-at'          => 'أنجز في',
            'activity-type'    => 'نوع النشاط',
            'summary'          => 'الملخص',
            'lead'             => 'عميل محتمل / فرصة',
            'assigned-to'      => 'مسند إلى',
            'done-by'          => 'أنجز بواسطة',
            'due-date'         => 'تاريخ الاستحقاق',
        ],
    ],

    'tabs' => [
        'leads'         => 'العملاء المحتملون',
        'opportunities' => 'الفرص',
        'mine'          => 'الخاصة بي',
        'won'           => 'المكسوبة',
        'lost'          => 'المفقودة',
        'my-activities' => 'أنشطتي',
        'this-month'    => 'هذا الشهر',
    ],
];
