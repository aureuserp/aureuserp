<?php

return [
    'title' => 'إدارة الضرائب',

    'form' => [
        'default-taxes' => [
            'label'       => 'الضرائب الافتراضية',
            'helper-text' => 'سيتم تطبيق الضريبة الافتراضية على المنتجات إذا لم يتم اختيار ضريبة',
        ],

        'sales-tax' => [
            'label' => 'ضريبة المبيعات',
        ],

        'purchase-tax' => [
            'label' => 'ضريبة المشتريات',
        ],

        'prices' => [
            'label' => 'الأسعار',
        ],

        'rounding-method' => [
            'label'       => 'طريقة التقريب',
            'helper-text' => 'الطريقة المستخدمة لتقريب مبالغ الضريبة',

            'options' => [
                'round-per-line' => 'تقريب لكل سطر',
                'round-globally' => 'تقريب إجمالي',
            ],
        ],

        'fiscal-country' => [
            'label' => 'البلد الضريبي',
        ],
    ],
];
