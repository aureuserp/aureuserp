<?php

return [
    'label' => 'Crear Factura',

    'action' => [
        'notification' => [
            'warning' => [
                'title' => 'No hay líneas facturables',
                'body'  => 'No hay líneas facturables, por favor asegúrate de que se haya recibido una cantidad.',
            ],

            'success' => [
                'title' => 'Factura creada',
                'body'  => 'La factura ha sido creada exitosamente.',
            ],
        ],
    ],
];
