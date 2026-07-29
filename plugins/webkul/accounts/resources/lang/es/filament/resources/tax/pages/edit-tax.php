<?php

return [
    'notification' => [
        'title' => 'Impuesto actualizado',
        'body'  => 'El impuesto se ha actualizado correctamente.',
    ],

    'header-actions' => [
        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Impuesto eliminado',
                    'body'  => 'El impuesto se ha eliminado correctamente.',
                ],

                'error' => [
                    'title' => 'No se pudo eliminar el impuesto',
                    'body'  => 'El impuesto no se puede eliminar porque está actualmente en uso.',
                ],

                'invalid-repartition-lines' => [
                    'title' => 'Líneas de reparto no válidas',
                ],
            ],
        ],
    ],
];
