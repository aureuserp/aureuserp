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
];
