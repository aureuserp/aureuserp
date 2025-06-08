<?php

return [
    'title' => 'Etiquetas',

    'navigation' => [
        'title' => 'Etiquetas',
        'group' => 'Empleado',
    ],

    'groups' => [
        'status'     => 'Estado',
        'created-by' => 'Creado Por',
        'created-at' => 'Creado El',
        'updated-at' => 'Actualizado El',
    ],

    'form' => [
        'fields' => [
            'name'  => 'Nombre',
            'color' => 'Color',
        ],
    ],

    'table' => [
        'columns' => [
            'id'         => 'ID',
            'name'       => 'Nombre',
            'color'      => 'Color',
            'created-by' => 'Creado Por',
            'created-at' => 'Creado El',
            'updated-at' => 'Actualizado El',
        ],

        'filters' => [
            'name'       => 'Nombre',
            'created-by' => 'Creado Por',
            'updated-by' => 'Actualizado Por',
            'updated-at' => 'Actualizado El',
            'created-at' => 'Creado El',
        ],

        'groups' => [
            'name'         => 'Nombre',
            'job-position' => 'Puesto de Trabajo',
            'color'        => 'Color',
            'created-by'   => 'Creado Por',
            'created-at'   => 'Creado El',
            'updated-at'   => 'Actualizado El',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Etiqueta actualizada',
                    'body'  => 'La etiqueta ha sido actualizada exitosamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Etiqueta eliminada',
                    'body'  => 'La etiqueta ha sido eliminada exitosamente.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Etiquetas eliminadas',
                    'body'  => 'Las etiquetas han sido eliminadas exitosamente.',
                ],
            ],
        ],

        'empty-state-action' => [
            'create' => [
                'notification' => [
                    'title' => 'Etiqueta creada',
                    'body'  => 'La etiqueta ha sido creada exitosamente.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'name'  => 'Nombre',
        'color' => 'Color',
    ],
];
