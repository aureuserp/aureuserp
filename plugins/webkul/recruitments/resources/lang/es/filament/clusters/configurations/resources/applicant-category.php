<?php

return [
    'title' => 'Etiquetas',

    'navigation' => [
        'title' => 'Etiquetas',
        'group' => 'Candidaturas',
    ],

    'form' => [
        'fields' => [
            'name'             => 'Nombre',
            'name-placeholder' => 'Introducir el nombre de las etiquetas',
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
            'employee'   => 'Empleado',
            'created-by' => 'Creado por',
            'updated-at' => 'Actualizado el',
            'created-at' => 'Creado el',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Etiquetas actualizadas',
                    'body'  => 'Las etiquetas se han actualizado correctamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Etiquetas eliminadas',
                    'body'  => 'Las etiquetas se han eliminado correctamente.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Categorías de candidato eliminadas',
                    'body'  => 'Las categorías de candidato se han eliminado correctamente.',
                ],
            ],
        ],

        'empty-state-action' => [
            'create' => [
                'notification' => [
                    'title' => 'Etiquetas creadas',
                    'body'  => 'Las etiquetas se han creado correctamente.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'name'  => 'Nombre',
        'color' => 'Color',
    ],
];
