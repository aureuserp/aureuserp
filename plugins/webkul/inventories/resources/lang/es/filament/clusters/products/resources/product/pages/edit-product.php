<?php

return [
    'before-save' => [
        'notification' => [
            'error' => [
                'tracking-update' => [
                    'title' => 'Error al actualizar el seguimiento',
                    'body'  => 'No se puede cambiar el seguimiento de inventario de un producto que ya ha sido utilizado.',
                ],

                'track-by-update' => [
                    'title' => 'Error al actualizar el seguimiento',
                    'body'  => 'Existen productos en stock que no tienen número de lote / serie. Se pueden asignar números de lote / serie realizando un ajuste de inventario.',
                ],
            ],
        ],
    ],
];
