<?php

return [
    'navigation' => [
        'title' => 'Empaques',
        'group' => 'Productos',
    ],

    'form' => [
        'package-type' => 'Tipo de paquete',
        'routes'       => 'Rutas',
    ],

    'table' => [
        'columns' => [
            'package-type' => 'Tipo de paquete',
        ],

        'groups' => [
            'package-type' => 'Tipo de paquete',
        ],

        'filters' => [
            'package-type' => 'Tipo de paquete',
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'entries' => [
                    'package_type' => 'Tipo de paquete',
                ],
            ],

            'routing' => [
                'title' => 'Información de ruteo',

                'entries' => [
                    'routes'     => 'Rutas del almacén',
                    'route_name' => 'Nombre de la Ruta',
                ],
            ],
        ],
    ],
];
