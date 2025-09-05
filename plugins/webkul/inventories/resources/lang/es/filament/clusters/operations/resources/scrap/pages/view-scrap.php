<?php

return [
    'header-actions' => [
        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Desecho Eliminado',
                    'body'  => 'El desecho ha sido eliminado exitosamente.',
                ],

                'error' => [
                    'title' => 'No se pudieron eliminar los desechos',
                    'body'  => 'Los desechos no se pueden eliminar porque están actualmente en uso.',
                ],
            ],
        ],
    ],
];
