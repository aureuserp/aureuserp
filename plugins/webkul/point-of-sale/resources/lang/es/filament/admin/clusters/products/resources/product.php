<?php

return [
    'navigation' => [
        'title' => 'Productos',
    ],

    'tabs' => [
        'pos' => 'TPV',
    ],

    'form' => [
        'sections' => [
            'point-of-sale' => [
                'title'  => 'Punto de venta',

                'fields' => [
                    'available-in-pos'        => 'Disponible en el TPV',
                    'available-in-pos-helper' => 'Haz que este producto esté disponible en los terminales del Punto de venta.',
                    'categories'              => 'Categorías de productos del TPV',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'point-of-sale' => [
                'title'   => 'Punto de venta',

                'entries' => [
                    'available-in-pos' => 'Disponible en el TPV',
                    'categories'       => 'Categorías de productos del TPV',
                ],
            ],
        ],
    ],
];
