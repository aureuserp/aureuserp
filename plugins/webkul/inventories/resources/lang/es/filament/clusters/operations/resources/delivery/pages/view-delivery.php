<?php

return [
    'header-actions' => [
        'print' => [
            'label' => 'Imprimir',
        ],

        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Entrega Eliminada',
                    'body'  => 'La entrega ha sido eliminada exitosamente.',
                ],

                'error' => [
                    'title' => 'No se pudo eliminar la entrega',
                    'body'  => 'La entrega no se puede eliminar porque está actualmente en uso.',
                ],
            ],
        ],
    ],
];
