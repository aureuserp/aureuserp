<?php

return [
    'navigation' => [
        'title' => 'Plantas',
        'group' => 'Punto de venta',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'General',

                'fields' => [
                    'name'                => 'Nombre',
                    'company'             => 'Empresa',
                    'background-color'    => 'Color de fondo',
                    'background-image'    => 'Imagen de fondo',
                    'configs'             => 'Punto de venta',
                    'configs-helper-text' => 'Terminales en modo restaurante que usan esta planta.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'General',

                'entries' => [
                    'name'             => 'Nombre',
                    'background-color' => 'Color de fondo',
                    'company-name'     => 'Empresa',
                    'configs'          => 'Punto de venta',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'             => 'Nombre',
            'tables'           => 'Mesas',
            'configs'          => 'Punto de venta',
            'background-color' => 'Color de fondo',
            'company'          => 'Empresa',
        ],
    ],
];
