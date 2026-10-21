<?php

return [
    'navigation' => [
        'title' => 'طابعات التحضير',
        'group' => 'نقطة البيع',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'عام',

                'fields' => [
                    'name'         => 'الاسم',
                    'printer-type' => 'نوع الطابعة',
                    'proxy-ip'     => 'عنوان IP للوكيل',
                    'company'      => 'الشركة',
                    'categories'   => 'الفئات المطبوعة',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'   => 'عام',

                'entries' => [
                    'name'         => 'الاسم',
                    'printer-type' => 'نوع الطابعة',
                    'proxy-ip'     => 'عنوان IP',
                    'company-name' => 'الشركة',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'         => 'الاسم',
            'printer-type' => 'نوع الطابعة',
            'proxy-ip'     => 'عنوان IP للوكيل',
            'categories'   => 'الفئات',
            'company'      => 'الشركة',
        ],

        'filters' => [
            'printer-type' => 'نوع الطابعة',
        ],
    ],
];
