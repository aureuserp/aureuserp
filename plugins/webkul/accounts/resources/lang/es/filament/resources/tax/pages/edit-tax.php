<?php

return [
    'notification' => [
        'title' => 'Impuesto actualizado',
        'body'  => 'El impuesto ha sido actualizado exitosamente.',
    ],

    'header-actions' => [
        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Impuesto eliminado',
                    'body'  => 'El impuesto ha sido eliminado exitosamente.',
                ],

                'error' => [
                    'title' => 'No se pudo eliminar el impuesto',
                    'body'  => 'El impuesto no puede ser eliminado porque está en uso actualmente.',
                ],
            ],
        ],
    ],
];
