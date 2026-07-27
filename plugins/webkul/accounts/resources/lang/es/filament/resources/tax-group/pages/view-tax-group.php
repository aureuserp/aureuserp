<?php

return [
    'header-actions' => [
        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Grupo de impuestos eliminado',
                    'body'  => 'El grupo de impuestos se ha eliminado correctamente.',
                ],

                'error' => [
                    'title' => 'No se pudo eliminar el grupo de impuestos',
                    'body'  => 'El grupo de impuestos no se puede eliminar porque está actualmente en uso.',
                ],
            ],
        ],
    ],
];
