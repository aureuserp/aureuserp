<?php

return [
    'notification' => [
        'title' => 'Grupo de impuestos actualizado',
        'body'  => 'El grupo de impuestos ha sido actualizado exitosamente.',
    ],

    'header-actions' => [
        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Grupo de impuestos eliminado',
                    'body'  => 'El grupo de impuestos ha sido eliminado exitosamente.',
                ],

                'error' => [
                    'title' => 'No se pudo eliminar el grupo de impuestos',
                    'body'  => 'El grupo de impuestos no puede ser eliminado porque está en uso actualmente.',
                ],
            ],
        ],
    ],
];
