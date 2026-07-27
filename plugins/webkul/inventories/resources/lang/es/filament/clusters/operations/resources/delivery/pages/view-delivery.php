<?php

return [
    'header-actions' => [
        'print' => [
            'label' => 'Imprimir',
        ],

        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Entrega eliminada',
                    'body'  => 'La entrega ha sido eliminada correctamente.',
                ],

                'error' => [
                    'title' => 'No se pudo eliminar la entrega',
                    'body'  => 'La entrega no puede eliminarse porque está en uso actualmente.',
                ],
            ],
        ],
    ],
];
