<?php

return [
    'title' => 'Etiquetas',

    'navigation' => [
        'title' => 'Etiquetas',
        'group' => 'Empleado',
    ],

    'groups' => [
        'status'     => 'Estado',
        'created-by' => 'Creado por',
        'created-at' => 'Creado el',
        'updated-at' => 'Actualizado el',
    ],

    'form' => [
        'fields' => [
            'name'             => 'Nombre',
            'name-placeholder' => 'Introducir el nombre de la etiqueta',
            'color'            => 'Color',
        ],
    ],

    'table' => [
        'columns' => [
            'id'         => 'ID',
            'name'       => 'Nombre',
            'color'      => 'Color',
            'created-by' => 'Creado por',
            'created-at' => 'Creado el',
            'updated-at' => 'Actualizado el',
        ],

        'filters' => [
            'name'       => 'Nombre',
            'created-by' => 'Creado por',
            'updated-by' => 'Actualizado por',
            'updated-at' => 'Actualizado el',
            'created-at' => 'Creado el',
        ],

        'groups' => [
            'name'         => 'Nombre',
            'job-position' => 'Puesto de trabajo',
            'color'        => 'Color',
            'created-by'   => 'Creado por',
            'created-at'   => 'Creado el',
            'updated-at'   => 'Actualizado el',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Etiqueta actualizada',
                    'body'  => 'La etiqueta se ha actualizado correctamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Etiqueta eliminada',
                    'body'  => 'La etiqueta se ha eliminado correctamente.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Etiquetas eliminadas',
                    'body'  => 'Las etiquetas se han eliminado correctamente.',
                ],
            ],
        ],

        'empty-state-action' => [
            'create' => [
                'notification' => [
                    'title' => 'Etiqueta creada',
                    'body'  => 'La etiqueta se ha creado correctamente.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'name'  => 'Nombre',
        'color' => 'Color',
    ],
];
