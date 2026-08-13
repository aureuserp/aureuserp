<?php

return [
    'notification' => [
        'title' => 'Producto actualizado',
        'body'  => 'El producto se ha actualizado correctamente.',
    ],

    'header-actions' => [
        'update-quantity' => [
            'label'                     => 'Actualizar cantidad',
            'modal-heading'             => 'Actualizar cantidad del producto',
            'modal-submit-action-label' => 'Actualizar',

            'form' => [
                'fields' => [
                    'on-hand-qty' => 'Cantidad disponible',
                ],
            ],
        ],

        'delete' => [
            'notification' => [
                'title' => 'Producto eliminado',
                'body'  => 'El producto se ha eliminado correctamente.',
            ],
        ],
    ],
];
