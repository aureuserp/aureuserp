<?php

return [
    'before-save' => [
        'notification' => [
            'error' => [
                'tracking-update' => [
                    'title' => 'Error al actualizar el seguimiento',
                    'body'  => 'No se puede cambiar el seguimiento de inventario de un producto que ya ha sido utilizado.',
                ],

                'reordering-rules' => [
                    'title' => 'Error al actualizar el producto',
                    'body'  => 'Todavía tiene algunas reglas de reabastecimiento activas en este producto. Archívelas o elimínelas primero.',
                ],

                'reserved' => [
                    'title' => 'Error al actualizar el seguimiento',
                    'body'  => 'No se puede cambiar el seguimiento de inventario de un producto que está actualmente reservado en un movimiento de stock. Si necesita cambiar el seguimiento de inventario, primero debe anular la reserva del movimiento de stock.',
                ],

                'qty-not-zero' => [
                    'title' => 'Error al actualizar el seguimiento',
                    'body'  => 'La cantidad disponible debe establecerse en cero antes de cambiar el seguimiento de inventario.',
                ],

                'track-by-update' => [
                    'title' => 'Error al actualizar el seguimiento',
                    'body'  => 'Existen productos en stock que no tienen número de lote / serie. Se pueden asignar números de lote / serie realizando un ajuste de inventario.',
                ],
            ],
        ],
    ],
];
