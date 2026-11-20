<?php

return [
    'permission-descriptions' => [
        'view_any'         => 'Listar y explorar todos los registros.',
        'view'             => 'Ver un registro en detalle.',
        'create'           => 'Crear nuevos registros.',
        'update'           => 'Editar y guardar registros existentes.',
        'delete'           => 'Eliminar un registro.',
        'delete_any'       => 'Eliminar varios registros a la vez.',
        'force_delete'     => 'Eliminar permanentemente un registro (sin papelera).',
        'force_delete_any' => 'Eliminar permanentemente varios registros.',
        'restore'          => 'Restaurar un registro archivado.',
        'restore_any'      => 'Restaurar varios registros archivados.',
        'reorder'          => 'Cambiar el orden de los registros.',
    ],

    'form' => [
        'fields' => [
            'web'     => 'Web',
            'sanctum' => 'Sanctum',
        ],
    ],

    'notification' => [
        'system-role-delete' => [
            'title' => 'No se puede eliminar el rol del sistema',
            'body'  => 'Este es un rol del sistema y no se puede eliminar.',
        ],
    ],

    'table' => [
        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Rol eliminado',
                    'body'  => 'El rol se ha eliminado correctamente.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Roles eliminados',
                    'body'  => 'Los roles seleccionados se han eliminado correctamente.',
                ],
            ],
        ],
    ],
];
