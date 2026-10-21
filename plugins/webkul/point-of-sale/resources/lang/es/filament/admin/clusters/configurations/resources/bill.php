<?php

return [
    'navigation' => [
        'title' => 'Monedas/Billetes',
        'group' => 'Punto de venta',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'General',

                'fields' => [
                    'name'                           => 'Nombre',
                    'value'                          => 'Valor de la moneda/billete',
                    'is-for-all-configs'             => 'Para todos los TPV',
                    'is-for-all-configs-helper-text' => 'Si se marca, esta moneda/billete estará disponible en todos los puntos de venta.',
                    'configs'                        => 'Puntos de venta',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'               => 'Nombre',
            'value'              => 'Valor de la moneda/billete',
            'is-for-all-configs' => 'Para todos los TPV',
            'configs'            => 'Puntos de venta',
            'company'            => 'Empresa',
        ],
    ],
];
