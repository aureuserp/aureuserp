<?php

return [
    'form' => [
        'name'       => 'Nombre',
        'short-name' => 'Nombre Corto',
    ],

    'table' => [
        'columns' => [
            'name'       => 'Nombre',
            'short-name' => 'Nombre Corto',
            'created-at' => 'Creado El',
            'updated-at' => 'Actualizado El',
        ],

        'filters' => [
            'creator' => 'Creador',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Título actualizado',
                    'body'  => 'El título ha sido actualizado exitosamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Título eliminado',
                    'body'  => 'El título ha sido eliminado exitosamente.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Títulos eliminados',
                    'body'  => 'Los títulos han sido eliminados exitosamente.',
                ],
            ],
        ],
    ],
];
