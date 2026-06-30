<?php

return [
    'title' => 'Crear factura',

    'modal' => [
        'heading' => 'Crear factura',
    ],

    'notification' => [
        'invoice-created' => [
            'title' => 'Factura creada',
            'body'  => 'La factura se ha creado correctamente.',
        ],

        'no-invoiceable-lines' => [
            'title' => 'No hay líneas facturables',
            'body'  => 'No hay ninguna línea facturable, asegúrese de que se haya recibido una cantidad.',
        ],
    ],

    'form' => [
        'fields' => [
            'create-invoice' => 'Crear factura',
        ],
    ],
];
