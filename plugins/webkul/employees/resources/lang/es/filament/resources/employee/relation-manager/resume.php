<?php

return [
    'form' => [
        'sections' => [
            'fields' => [
                'title'        => 'Título',
                'type'         => 'Tipo',
                'name'         => 'Nombre',
                'create-type'  => 'Crear Tipo',
                'duration'     => 'Duración',
                'start-date'   => 'Fecha de Inicio',
                'end-date'     => 'Fecha de Fin',
                'display-type' => 'Tipo de Visualización',
                'description'  => 'Descripción',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'title'        => 'Título',
            'start-date'   => 'Fecha de Inicio',
            'end-date'     => 'Fecha de Fin',
            'display-type' => 'Tipo de Visualización',
            'description'  => 'Descripción',
            'created-by'   => 'Creado Por',
            'created-at'   => 'Creado El',
            'updated-at'   => 'Actualizado El',
        ],

        'groups' => [
            'group-by-type'         => 'Agrupar por Tipo',
            'group-by-display-type' => 'Agrupar por Tipo de Visualización',
        ],

        'header-actions' => [
            'add-resume' => 'Añadir Currículum',
        ],

        'filters' => [
            'type'          => 'Tipo',
            'start-date-from' => 'Fecha de Inicio Desde',
            'start-date-to'   => 'Fecha de Inicio Hasta',
            'created-from'    => 'Creado Desde',
            'created-to'      => 'Creado Hasta',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Nivel de Habilidad actualizado',
                    'body'  => 'El nivel de habilidad ha sido actualizado exitosamente.',
                ],
            ],

            'create' => [
                'notification' => [
                    'title' => 'Nivel de Habilidad creado',
                    'body'  => 'El nivel de habilidad ha sido creado exitosamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Nivel de Habilidad eliminado',
                    'body'  => 'El nivel de habilidad ha sido eliminado exitosamente.',
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
            'title'        => 'Título',
            'display-type' => 'Tipo de Visualización',
            'type'         => 'Tipo',
            'description'  => 'Descripción',
            'duration'     => 'Duración',
            'start-date'   => 'Fecha de Inicio',
            'end-date'     => 'Fecha de Fin',
        ],
    ],
];
