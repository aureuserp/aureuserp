<?php

return [
    'notification' => [
        'title' => 'Producto actualizado',
        'body'  => 'El producto ha sido actualizado exitosamente.',
    ],

    'header-actions' => [
        'update-quantity' => [
            'label'                     => 'Actualizar Cantidad',
            'modal-heading'             => 'Actualizar Cantidad del Producto',
            'modal-submit-action-label' => 'Actualizar',

            'form' => [
                'fields' => [
                    'on-hand-qty' => 'Cantidad Disponible',
                ],
            ],
        ],

        'delete' => [
            'notification' => [
                'title' => 'Producto eliminado',
                'body'  => 'El producto ha sido eliminado exitosamente.',
            ],
        ],
    ],
];
