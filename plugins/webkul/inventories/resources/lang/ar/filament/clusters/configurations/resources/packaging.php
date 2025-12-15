<?php

return [
    'navigation' => [
        'title' => 'التغليف',
        'group' => 'المنتجات',
    ],

    'form' => [
        'package-type' => 'نوع العبوة',
        'routes'       => 'المسارات',
    ],

    'table' => [
        'columns' => [
            'package-type' => 'نوع العبوة',
        ],

        'groups' => [
            'package-type' => 'نوع العبوة',
        ],

        'filters' => [
            'package-type' => 'نوع العبوة',
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'entries' => [
                    'package_type' => 'نوع العبوة',
                ],
            ],

            'routing' => [
                'title' => 'معلومات التوجيه',

                'entries' => [
                    'routes'     => 'مسارات المستودع',
                    'route_name' => 'اسم المسار',
                ],
            ],
        ],
    ],
];
