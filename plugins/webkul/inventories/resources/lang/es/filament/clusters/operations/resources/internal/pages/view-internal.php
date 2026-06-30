<?php

return [
    'header-actions' => [
        'print' => [
            'label' => 'Imprimir',
        ],

        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Transferencia interna eliminada',
                    'body'  => 'La transferencia interna ha sido eliminada correctamente.',
                ],

                'error' => [
                    'title' => 'No se pudo eliminar la transferencia interna',
                    'body'  => 'La transferencia interna no puede eliminarse porque está en uso actualmente.',
                ],
            ],
        ],
    ],
];
