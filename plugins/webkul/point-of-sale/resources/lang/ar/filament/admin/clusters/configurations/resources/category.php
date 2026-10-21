<?php

return [
    'navigation' => [
        'title' => 'فئات منتجات نقطة البيع',
        'group' => 'نقطة البيع',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'عام',

                'fields' => [
                    'name'    => 'الاسم',
                    'parent'  => 'الفئة الأصلية',
                    'company' => 'الشركة',
                    'color'   => 'اللون',
                    'image'   => 'الصورة',
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
                    'parent-name'  => 'الفئة الأصلية',
                    'color'        => 'اللون',
                    'company-name' => 'الشركة',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'    => 'الاسم',
            'parent'  => 'الفئة الأصلية',
            'color'   => 'اللون',
            'company' => 'الشركة',
        ],

        'groups' => [
            'parent' => 'الفئة الأصلية',
        ],
    ],
];
