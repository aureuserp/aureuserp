<?php

return [
    'navigation' => [
        'title' => 'Catégories',
        'group' => 'Produits',
    ],

    'form' => [
        'sections' => [
            'inventory' => [
                'title' => 'Inventaire',

                'fieldsets' => [
                    'logistics' => [
                        'title' => 'Logistique',

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
                'title' => 'Inventaire',

                'subsections' => [
                    'logistics' => [
                        'title' => 'Logistique',

                        'entries' => [
                            'routes'     => 'Routes de l\'entrepôt',
                            'route_name' => 'Nom de la route',
                        ],
                    ],
                ],
            ],
        ],
    ],
];
