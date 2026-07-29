<?php

return [
    'title' => 'Etapas',

    'navigation' => [
        'title' => 'Etapas',
        'group' => 'Puestos de trabajo',
    ],

    'form' => [
        'sections' => [
            'general-information' => [
                'title' => 'Información general',

                'fields' => [
                    'stage-name'   => 'Nombre de la etapa',
                    'sort'         => 'Orden de secuencia',
                    'requirements' => 'Requisitos',
                ],
            ],

            'tooltips' => [
                'title'       => 'Descripciones emergentes',
                'description' => 'Definir la etiqueta personalizada para el estado de la candidatura.',

                'fields' => [
                    'gray-label'          => 'Etiqueta gris',
                    'gray-label-tooltip'  => 'La etiqueta para el estado gris.',
                    'red-label'           => 'Etiqueta roja',
                    'red-label-tooltip'   => 'La etiqueta para el estado rojo.',
                    'green-label'         => 'Etiqueta verde',
                    'green-label-tooltip' => 'La etiqueta para el estado verde.',
                ],
            ],

            'additional-information' => [
                'title' => 'Información adicional',

                'fields' => [
                    'job-positions' => 'Puestos de trabajo',
                    'folded'        => 'Plegado',
                    'hired-stage'   => 'Etapa de contratación',
                    'default-stage' => 'Etapa predeterminada',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'id'                 => 'ID',
            'name'               => 'Nombre de la etapa',
            'hired-stage'        => 'Etapa de contratación',
            'default-stage'      => 'Etapa predeterminada',
            'folded'             => 'Plegado',
            'job-positions'      => 'Puestos de trabajo',
            'created-by'         => 'Creado por',
            'created-at'         => 'Creado el',
            'updated-at'         => 'Actualizado el',
        ],

        'filters' => [
            'name'         => 'Nombre de la etapa',
            'job-position' => 'Puesto de trabajo',
            'folded'       => 'Plegado',
            'gray-label'   => 'Etiqueta gris',
            'red-label'    => 'Etiqueta roja',
            'green-label'  => 'Etiqueta verde',
            'created-by'   => 'Creado por',
            'created-at'   => 'Creado el',
            'updated-at'   => 'Actualizado el',
        ],

        'groups' => [
            'job-position' => 'Puesto de trabajo',
            'stage-name'   => 'Nombre de la etapa',
            'folded'       => 'Plegado',
            'gray-label'   => 'Etiqueta gris',
            'red-label'    => 'Etiqueta roja',
            'green-label'  => 'Etiqueta verde',
            'created-by'   => 'Creado por',
            'created-at'   => 'Creado el',
            'updated-at'   => 'Actualizado el',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Etapas eliminadas',
                        'body'  => 'Las etapas se han eliminado correctamente.',
                    ],

                    'error' => [
                        'title' => 'No se pudieron eliminar las etapas',
                        'body'  => 'Las etapas no se pueden eliminar porque están actualmente en uso.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Etapas eliminadas',
                    'body'  => 'Las etapas se han eliminado correctamente.',
                ],
            ],
        ],

        'empty-state-actions' => [
            'create' => [
                'label' => 'Nueva etapa',
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general-information' => [
                'title' => 'Información general',

                'entries' => [
                    'stage-name'   => 'Nombre de la etapa',
                    'sort'         => 'Orden de secuencia',
                    'requirements' => 'Requisitos',
                ],
            ],

            'tooltips' => [
                'title'       => 'Descripciones emergentes',
                'description' => 'Definir la etiqueta personalizada para el estado de la candidatura.',

                'entries' => [
                    'gray-label'          => 'Etiqueta gris',
                    'gray-label-tooltip'  => 'La etiqueta para el estado gris.',
                    'red-label'           => 'Etiqueta roja',
                    'red-label-tooltip'   => 'La etiqueta para el estado rojo.',
                    'green-label'         => 'Etiqueta verde',
                    'green-label-tooltip' => 'La etiqueta para el estado verde.',
                ],
            ],

            'additional-information' => [
                'title' => 'Información adicional',

                'entries' => [
                    'job-positions'      => 'Puesto de trabajo',
                    'folded'             => 'Plegado',
                    'hired-stage'        => 'Etapa de contratación',
                    'default-stage'      => 'Etapa predeterminada',
                ],
            ],
        ],
    ],

];
