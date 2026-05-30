<?php

return [
    'title' => 'Gestionar almacenes',

    'form' => [
        'enable-locations'                      => 'Ubicaciones',
        'enable-locations-helper-text'          => 'Rastrea la ubicación de productos en tu almacén',
        'configure-locations'                   => 'Configurar ubicaciones',
        'enable-multi-steps-routes'             => 'Rutas de Múltiples Pasos',
        'enable-multi-steps-routes-helper-text' => 'Usa tus propias rutas para gestionar la transferencia de productos entre almacenes',
        'configure-routes'                      => 'Configurar Rutas de Almacén',
    ],

    'before-save' => [
        'notification' => [
            'warning' => [
                'title' => 'Tiene múltiples almacenes',
                'body'  => 'No es posible desactivar la multi-ubicación si hay más de un almacén.',
            ],
        ],
    ],
];
