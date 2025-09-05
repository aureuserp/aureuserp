<?php

return [
    'form' => [
        'sections' => [
            'fields' => [
                'skill-type'  => 'Tipo de Habilidad',
                'skill'       => 'Habilidad',
                'skill-level' => 'Nivel de Habilidad',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'skill-type'  => 'Tipo de Habilidad',
            'skill'       => 'Habilidad',
            'skill-level' => 'Nivel de Habilidad',
            'level-percent' => 'Porcentaje de Nivel',
            'created-by'  => 'Creado Por',
            'user'        => 'Usuario',
            'created-at'  => 'Creado El',
        ],

        'groups' => [
            'skill-type' => 'Tipo de Habilidad',
        ],

        'header-actions' => [
            'add-skill' => 'Añadir Habilidad',
        ],

        'filters' => [
            'activity-type'   => 'Tipo de Actividad',
            'activity-status' => 'Estado de Actividad',
            'has-delay'       => 'Tiene Retraso',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Habilidad actualizada',
                    'body'  => 'La habilidad ha sido actualizada exitosamente.',
                ],
            ],

            'create' => [
                'notification' => [
                    'title' => 'Habilidad creada',
                    'body'  => 'La habilidad ha sido creada exitosamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Habilidad eliminada',
                    'body'  => 'La habilidad ha sido eliminada exitosamente.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Habilidades eliminadas',
                    'body'  => 'Las habilidades han sido eliminadas exitosamente.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'skill-type'  => 'Tipo de Habilidad',
            'skill'       => 'Habilidad',
            'skill-level' => 'Nivel de Habilidad',
            'level-percent' => 'Porcentaje de Nivel',
        ],
    ],
];
