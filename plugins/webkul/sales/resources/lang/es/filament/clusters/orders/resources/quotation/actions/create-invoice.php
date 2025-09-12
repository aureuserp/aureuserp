<?php

return [
    'title' => 'Crear Factura',

    'modal' => [
        'heading' => 'Crear Factura',
    ],

    'notification' => [
        'invoice-created' => [
            'title' => 'Factura creada',
            'body'  => 'La factura ha sido creada exitosamente.',
        ],

        'no-invoiceable-lines' => [
            'title' => 'No hay líneas facturables',
            'body'  => 'No hay ninguna línea facturable, por favor asegúrese de que se haya recibido una cantidad.',
        ],
    ],

    'form' => [
        'fields' => [
            'create-invoice' => 'Crear Factura',
        ],
    ],
];
