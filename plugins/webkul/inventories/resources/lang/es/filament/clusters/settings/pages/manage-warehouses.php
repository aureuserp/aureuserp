<?php

return [
    'title' => 'Gestionar almacenes',

    'form' => [
        'enable-locations'                      => 'Ubicaciones',
        'enable-locations-helper-text'          => 'Rastrear la ubicación de productos en el almacén.',
        'configure-locations'                   => 'Configurar ubicaciones',
        'enable-multi-steps-routes'             => 'Rutas de Múltiples Pasos',
        'enable-multi-steps-routes-helper-text' => 'Usar rutas propias para gestionar la transferencia de productos entre almacenes.',
        'configure-routes'                      => 'Configurar Rutas de Almacén',
    ],

    'before-save' => [
        'notification' => [
            'warning' => [
                'title' => 'Hay múltiples almacenes',
                'body'  => 'No es posible desactivar la multi-ubicación si hay más de un almacén.',
            ],
        ],
    ],
];
