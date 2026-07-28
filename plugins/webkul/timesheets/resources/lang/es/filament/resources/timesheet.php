<?php

return [
    'title' => 'Partes de horas',

    'navigation' => [
        'title' => 'Partes de horas',
    ],

    'global-search' => [
        'project' => 'Proyecto',
        'task'    => 'Tarea',
        'date'    => 'Fecha',
    ],

    'form' => [
        'date'                   => 'Fecha',
        'employee'               => 'Empleado',
        'project'                => 'Proyecto',
        'task'                   => 'Tarea',
        'description'            => 'Descripción',
        'time-spent'             => 'Tiempo dedicado',
        'time-spent-helper-text' => 'Tiempo dedicado en horas (ej. 1.5 horas significa 1 hora 30 minutos)',
    ],

    'table' => [
        'columns' => [
            'date'        => 'Fecha',
            'employee'    => 'Empleado',
            'project'     => 'Proyecto',
            'task'        => 'Tarea',
            'description' => 'Descripción',
            'time-spent'  => 'Tiempo dedicado',
            'created-at'  => 'Creado el',
            'updated-at'  => 'Actualizado el',
        ],

        'groups' => [
            'date'       => 'Fecha',
            'employee'   => 'Empleado',
            'project'    => 'Proyecto',
            'task'       => 'Tarea',
            'creator'    => 'Creador',
        ],

        'filters' => [
            'date-from'  => 'Fecha desde',
            'date-until' => 'Fecha hasta',
            'employee'   => 'Empleado',
            'project'    => 'Proyecto',
            'task'       => 'Tarea',
            'creator'    => 'Creador',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Parte de horas actualizado',
                    'body'  => 'El parte de horas se ha actualizado correctamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Parte de horas eliminado',
                    'body'  => 'El parte de horas se ha eliminado correctamente.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Partes de horas eliminados',
                    'body'  => 'Los partes de horas se han eliminado correctamente.',
                ],
            ],
        ],
    ],
];
