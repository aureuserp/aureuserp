<?php

return [
    'label' => 'Crear factura',

    'action' => [
        'notification' => [
            'warning' => [
                'title' => 'No hay líneas facturables',
                'body'  => 'No hay ninguna línea facturable, asegúrese de que se haya recibido una cantidad.',
            ],

            'success' => [
                'title' => 'Factura creada',
                'body'  => 'La factura se ha creado correctamente.',
            ],
        ],
    ],
];
