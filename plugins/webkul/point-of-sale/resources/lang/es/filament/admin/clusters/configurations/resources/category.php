<?php

return [
    'navigation' => [
        'title' => 'Categorías de productos del TPV',
        'group' => 'Punto de venta',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'General',

                'fields' => [
                    'name'    => 'Nombre',
                    'parent'  => 'Categoría padre',
                    'company' => 'Empresa',
                    'color'   => 'Color',
                    'image'   => 'Imagen',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'   => 'General',

                'entries' => [
                    'name'         => 'Nombre',
                    'parent-name'  => 'Categoría padre',
                    'color'        => 'Color',
                    'company-name' => 'Empresa',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'    => 'Nombre',
            'parent'  => 'Categoría padre',
            'color'   => 'Color',
            'company' => 'Empresa',
        ],

        'groups' => [
            'parent' => 'Categoría padre',
        ],
    ],
];
