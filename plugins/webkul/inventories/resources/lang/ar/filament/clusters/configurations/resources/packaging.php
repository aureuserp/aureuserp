<?php

return [
    'navigation' => [
        'title' => 'Packagings',
        'group' => 'Products',
    ],

    'form' => [
        'package-type' => 'Package Type',
        'routes'       => 'Routes',
    ],

    'table' => [
        'columns' => [
            'package-type' => 'Package Type',
        ],

        'groups' => [
            'package-type' => 'Package Type',
        ],

        'filters' => [
            'package-type' => 'Package Type',
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'entries' => [
                    'package_type' => 'Package Type',
                ],
            ],

            'routing' => [
                'title' => 'Routing Information',

                'entries' => [
                    'routes'     => 'Warehouse Routes',
                    'route_name' => 'Route Name',
                ],
            ],
        ],
    ],
];
