<?php

return [
    'header-actions' => [
        'print' => [
            'label' => 'Imprimir',
        ],

        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Transferencia Interna Eliminada',
                    'body'  => 'La transferencia interna ha sido eliminada exitosamente.',
                ],

                'error' => [
                    'title' => 'No se pudo eliminar la transferencia interna',
                    'body'  => 'La transferencia interna no se puede eliminar porque está actualmente en uso.',
                ],
            ],
        ],
    ],
];
