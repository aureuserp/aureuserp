<?php

return [
    'navigation' => [
        'title' => 'Ver lista de precios de proveedor',
    ],

    'header-actions' => [
        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Precio de proveedor eliminado',
                    'body'  => 'El precio de proveedor se ha eliminado correctamente.',
                ],

                'error' => [
                    'title' => 'No se pudo eliminar el precio de proveedor',
                    'body'  => 'El precio de proveedor no se puede eliminar porque está actualmente en uso.',
                ],
            ],
        ],
    ],
];
