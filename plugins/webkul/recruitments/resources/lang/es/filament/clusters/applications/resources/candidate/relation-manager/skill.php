<?php

return [
    'form' => [
        'sections' => [
            'fields' => [
                'skill-type'  => 'Tipo de competencia',
                'skill'       => 'Competencia',
                'skill-level' => 'Nivel de competencia',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'skill-type'    => 'Tipo de competencia',
            'skill'         => 'Competencia',
            'skill-level'   => 'Nivel de competencia',
            'level-percent' => 'Porcentaje de nivel',
            'created-by'    => 'Creado por',
            'user'          => 'Usuario',
            'created-at'    => 'Creado el',
        ],

        'groups' => [
            'skill-type' => 'Tipo de competencia',
        ],

        'header-actions' => [
            'add-skill' => 'Agregar competencia',
        ],

        'filters' => [
            'activity-type'   => 'Tipo de actividad',
            'activity-status' => 'Estado de la actividad',
            'has-delay'       => 'Tiene retraso',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Competencia actualizada',
                    'body'  => 'La competencia se ha actualizado correctamente.',
                ],
            ],

            'create' => [
                'notification' => [
                    'title' => 'Competencia creada',
                    'body'  => 'La competencia se ha creado correctamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Competencia eliminada',
                    'body'  => 'La competencia se ha eliminado correctamente.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Competencias eliminadas',
                    'body'  => 'Las competencias se han eliminado correctamente.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'skill-type'    => 'Tipo de competencia',
            'skill'         => 'Competencia',
            'skill-level'   => 'Nivel de competencia',
            'level-percent' => 'Porcentaje de nivel',
        ],
    ],
];
