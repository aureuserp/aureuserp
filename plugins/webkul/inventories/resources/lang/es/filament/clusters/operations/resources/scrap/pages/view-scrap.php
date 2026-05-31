<?php

return [
    'header-actions' => [
        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Merma eliminada',
                    'body'  => 'La merma ha sido eliminada correctamente.',
                ],

                'error' => [
                    'title' => 'No se pudieron eliminar las mermas',
                    'body'  => 'Las mermas no pueden eliminarse porque están en uso.',
                ],
            ],
        ],
    ],
];
