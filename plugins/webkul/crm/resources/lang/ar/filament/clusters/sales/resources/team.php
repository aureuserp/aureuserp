<?php

return [
    'model-label'        => 'فريق المبيعات',
    'plural-model-label' => 'فرق المبيعات',

    'navigation' => [
        'title' => 'الفرق',
    ],

    'table' => [
        'columns' => [
            'name'               => 'فريق المبيعات',
            'open-opportunities' => 'الفرص المفتوحة',
            'expected-revenue'   => 'الإيرادات المتوقعة',
            'members'            => 'الأعضاء',
            'company'            => 'الشركة',
        ],

        'filters' => [
            'team-leader' => 'قائد الفريق',
            'created-at'  => 'تاريخ الإنشاء',
        ],

        'actions' => [
            'pipeline' => [
                'label' => 'مسار المبيعات',
            ],
        ],
    ],

    'grid' => [
        'quotations'         => '{1} :count عرض سعر|[0,*] :count عروض أسعار',
        'configuration'      => 'الإعدادات',
        'menu'               => 'قائمة الفريق',
        'open-opportunities' => '{1} :count فرصة مفتوحة|[0,*] :count فرص مفتوحة',
        'orders-to-invoice'  => '{1} :count طلب للفوترة|[0,*] :count طلبات للفوترة',
        'chart'              => 'الفرص الجديدة أسبوعيًا',
        'sample-data'        => 'بيانات نموذجية',
        'new'                => 'جديد',
        'invoiced-month'     => 'المفوتر هذا الشهر',
        'of-target'          => ':invoiced من :target',
    ],

    'grid-menu' => [
        'view'          => 'عرض',
        'reporting'     => 'التقارير',
        'leads'         => 'العملاء المحتملون',
        'opportunities' => 'الفرص',
        'quotations'    => 'عروض الأسعار',
        'sales-orders'  => 'أوامر البيع',
        'forecast'      => 'التوقعات',
        'activities'    => 'الأنشطة',
        'color'         => 'اللون',
    ],
];
