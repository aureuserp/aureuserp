<?php

return [
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
            ],
        ],
    ],
];
