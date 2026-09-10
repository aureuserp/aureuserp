<?php

return [
    'title' => 'Ver moneda',

    'header-actions' => [
        'delete' => [
            'notification' => [
                'title' => 'Moneda eliminada',
                'body'  => 'La moneda se ha eliminado correctamente.',

                'error' => [
                    'title' => 'No se pudo eliminar la moneda',
                    'body'  => 'La moneda no se puede eliminar porque está en uso.',
                ],
            ],
        ],
    ],
];
