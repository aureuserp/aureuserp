<?php

return [
    'title' => 'Tareas',

    'header-actions' => [
        'create' => [
            'label' => 'Nueva tarea',
        ],
    ],

    'table' => [
        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Tarea restaurada',
                    'body'  => 'La tarea se ha restaurado correctamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Tarea eliminada',
                    'body'  => 'La tarea se ha eliminado correctamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Tarea eliminada permanentemente',
                    'body'  => 'La tarea se ha eliminado permanentemente correctamente.',
                ],
            ],
        ],
    ],

    'tabs' => [
        'open-tasks'       => 'Tareas abiertas',
        'my-tasks'         => 'Mis tareas',
        'unassigned-tasks' => 'Tareas sin asignar',
        'closed-tasks'     => 'Tareas cerradas',
        'starred-tasks'    => 'Tareas destacadas',
        'archived-tasks'   => 'Tareas archivadas',
    ],
];
