<?php

return [
    'navigation' => [
        'title' => 'الفئات',
        'group' => 'المنتجات',
    ],

    'form' => [
        'sections' => [
            'inventory' => [
                'title' => 'المخزون',

                'fieldsets' => [
                    'logistics' => [
                        'title' => 'اللوجستيات',

                        'fields' => [
                            'routes' => 'المسارات',
                        ],
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'inventory' => [
                'title' => 'المخزون',

                'subsections' => [
                    'logistics' => [
                        'title' => 'اللوجستيات',

                        'entries' => [
                            'routes'     => 'مسارات المستودع',
                            'route_name' => 'اسم المسار',
                        ],
                    ],
                ],
            ],
        ],
    ],
];
