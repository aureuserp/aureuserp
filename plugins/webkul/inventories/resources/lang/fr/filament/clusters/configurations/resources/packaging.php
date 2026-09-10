<?php

return [
    'navigation' => [
        'title' => 'Conditionnements',
        'group' => 'Produits',
    ],

    'form' => [
        'package-type' => 'Type de colis',
        'routes'       => 'Routes',
    ],

    'table' => [
        'columns' => [
            'package-type' => 'Type de colis',
        ],

        'groups' => [
            'package-type' => 'Type de colis',
        ],

        'filters' => [
            'package-type' => 'Type de colis',
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'entries' => [
                    'package_type' => 'Type de colis',
                ],
            ],

            'routing' => [
                'title' => 'Informations de routage',

                'entries' => [
                    'routes'     => 'Routes de l\'entrepôt',
                    'route_name' => 'Nom de la route',
                ],
            ],
        ],
    ],
];
