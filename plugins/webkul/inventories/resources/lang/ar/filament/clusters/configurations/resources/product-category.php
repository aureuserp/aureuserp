<?php

return [
    'navigation' => [
        'title' => 'Categories',
        'group' => 'Products',
    ],

    'form' => [
        'sections' => [
            'inventory' => [
                'title' => 'Inventory',

                'fieldsets' => [
                    'logistics' => [
                        'title' => 'Logistics',

                        'fields' => [
                            'routes' => 'Routes',
                        ],
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'inventory' => [
                'title' => 'Inventory',

                'subsections' => [
                    'logistics' => [
                        'title' => 'Logistics',

                        'entries' => [
                            'routes'     => 'Warehouse Routes',
                            'route_name' => 'Route Name',
                        ],
                    ],
                ],
            ],
        ],
    ],
];
