<?php

return [
    'header-actions' => [
        'print' => [
            'label' => 'Imprimir',
        ],

        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Lote eliminado',
                    'body'  => 'El lote ha sido eliminado correctamente.',
                ],

                'error' => [
                    'title' => 'No se pudo eliminar el lote',
                    'body'  => 'El lote no puede eliminarse porque está en uso actualmente.',
                ],
            ],
        ],
    ],
];
