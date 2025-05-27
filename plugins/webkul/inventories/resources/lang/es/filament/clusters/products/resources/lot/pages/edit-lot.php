<?php

return [
    'notification' => [
        'title' => 'Lote actualizado',
        'body'  => 'El lote ha sido actualizado exitosamente.',
    ],

    'header-actions' => [
        'print' => [
            'label' => 'Imprimir',
        ],

        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Lote eliminado',
                    'body'  => 'El lote ha sido eliminado exitosamente.',
                ],

                'error' => [
                    'title' => 'No se pudo eliminar el lote',
                    'body'  => 'El lote no se puede eliminar porque está actualmente en uso.',
                ],
            ],
        ],
    ],
];
