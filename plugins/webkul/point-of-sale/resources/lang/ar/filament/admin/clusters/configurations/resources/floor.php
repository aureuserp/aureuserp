<?php

return [
    'navigation' => [
        'title' => 'الطوابق',
        'group' => 'نقطة البيع',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'عام',

                'fields' => [
                    'name'                => 'الاسم',
                    'company'             => 'الشركة',
                    'background-color'    => 'لون الخلفية',
                    'background-image'    => 'صورة الخلفية',
                    'configs'             => 'نقطة البيع',
                    'configs-helper-text' => 'محطات وضع المطعم التي تستخدم هذا الطابق.',
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
                    'background-color' => 'لون الخلفية',
                    'company-name'     => 'الشركة',
                    'configs'          => 'نقطة البيع',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'             => 'الاسم',
            'tables'           => 'الطاولات',
            'configs'          => 'نقطة البيع',
            'background-color' => 'لون الخلفية',
            'company'          => 'الشركة',
        ],
    ],
];
