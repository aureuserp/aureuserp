<?php

return [
    'before-save' => [
        'notification' => [
            'error' => [
                'tracking-update' => [
                    'title' => 'Error al actualizar el seguimiento',
                    'body'  => 'No puede cambiar el seguimiento de inventario de un producto que ya ha sido utilizado.',
                ],

                'track-by-update' => [
                    'title' => 'Error al actualizar el seguimiento',
                    'body'  => 'Tiene productos en existencias que no tienen lote/número de serie. Puede asignarles lotes/números de serie realizando un ajuste de inventario.',
                ],
            ],
        ],
    ],

    'header-actions' => [
        'update-quantity' => [
            'label'                     => 'Actualizar cantidad',
            'modal-heading'             => 'Actualizar cantidad del producto',
            'modal-submit-action-label' => 'Actualizar',

            'form' => [
                'fields' => [
                    'product'     => 'Producto',
                    'on-hand-qty' => 'Cantidad disponible',
                ],
            ],
        ],
    ],
];
