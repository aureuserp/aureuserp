<?php

return [
    'notification' => [
        'title' => 'Transferencia interna actualizada',
        'body'  => 'La transferencia interna ha sido actualizada exitosamente.',
    ],

    'header-actions' => [
        'print' => [
            'label' => 'Imprimir',
        ],

        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Transferencia interna eliminada',
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
