<?php

return [
    'navigation' => [
        'title' => 'Embalajes',
        'group' => 'Productos',
    ],

    'form' => [
        'package-type' => 'Tipo de Embalaje',
        'routes'       => 'Rutas',
    ],

    'table' => [
        'columns' => [
            'package-type' => 'Tipo de Embalaje',
        ],

        'groups' => [
            'package-type' => 'Tipo de Embalaje',
        ],

        'filters' => [
            'package-type' => 'Tipo de Embalaje',
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'entries' => [
                    'package_type' => 'Tipo de Embalaje',
                ],
            ],

            'routing' => [
                'title'   => 'Información de Enrutamiento',
                'entries' => [
                    'routes'     => 'Rutas de Almacén',
                    'route_name' => 'Nombre de la Ruta',
                ],
            ],
        ],
    ],
];
