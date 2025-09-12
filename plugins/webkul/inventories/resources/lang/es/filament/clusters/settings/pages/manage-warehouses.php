<?php

return [
    'title' => 'Gestionar Almacenes',

    'form' => [
        'enable-locations'                      => 'Ubicaciones',
        'enable-locations-helper-text'          => 'Realice un seguimiento de la ubicación del producto en su almacén',
        'configure-locations'                   => 'Configurar Ubicaciones',
        'enable-multi-steps-routes'             => 'Rutas de Múltiples Pasos',
        'enable-multi-steps-routes-helper-text' => 'Utilice sus propias rutas para gestionar la transferencia de productos entre almacenes',
        'configure-routes'                      => 'Configurar Rutas de Almacén',
    ],

    'before-save' => [
        'notification' => [
            'warning' => [
                'title' => 'Tiene múltiples almacenes',
                'body'  => 'No puede desactivar la multi-ubicación si tiene más de un almacén.',
            ],
        ],
    ],
];
