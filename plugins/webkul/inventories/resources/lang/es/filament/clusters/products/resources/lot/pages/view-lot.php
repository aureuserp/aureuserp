<?php

return [
    'header-actions' => [
        'print' => [
            'label' => 'Imprimir',
        ],

        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Lote Eliminado',
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
