<?php

return [
    'navigation' => [
        'title' => 'Categorías',
        'group' => 'Productos',
    ],

    'form' => [
        'sections' => [
            'inventory' => [
                'title' => 'Inventario',

                'fieldsets' => [
                    'logistics' => [
                        'title' => 'Logística',

                        'fields' => [
                            'routes' => 'Rutas',
                        ],
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'inventory' => [
                'title' => 'Inventario',

                'subsections' => [
                    'logistics' => [
                        'title' => 'Logística',

                        'entries' => [
                            'routes'     => 'Rutas de Almacén',
                            'route_name' => 'Nombre de la Ruta',
                        ],
                    ],
                ],
            ],
        ],
    ],
];
