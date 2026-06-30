<?php

return [
    'navigation' => [
        'title' => 'Editar lista de precios de proveedor',
    ],

    'notification' => [
        'title' => 'Precio de proveedor actualizado',
        'body'  => 'El precio de proveedor se ha actualizado correctamente.',
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
                    'body'  => 'El precio de proveedor no se puede eliminar porque está en uso actualmente.',
                ],
            ],
        ],
    ],
];
