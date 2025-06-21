<?php

return [
    'navigation' => [
        'title' => 'Ver Lista de Precios de Proveedor',
    ],

    'header-actions' => [
        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Precio de proveedor eliminado',
                    'body'  => 'El precio de proveedor ha sido eliminado exitosamente.',
                ],

                'error' => [
                    'title' => 'No se pudo eliminar el precio de proveedor',
                    'body'  => 'El precio de proveedor no puede ser eliminado porque está actualmente en uso.',
                ],
            ],
        ],
    ],
];
