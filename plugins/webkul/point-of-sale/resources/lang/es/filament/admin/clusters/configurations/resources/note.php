<?php

return [
    'navigation' => [
        'title' => 'Modelos de nota',
        'group' => 'Punto de venta',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'General',

                'fields' => [
                    'name'  => 'Nota',
                    'color' => 'Color',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'    => 'Nota',
            'color'   => 'Color',
            'company' => 'Empresa',
        ],
    ],
];
