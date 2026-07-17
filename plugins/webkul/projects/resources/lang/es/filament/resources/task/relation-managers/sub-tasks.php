<?php

return [
    'table' => [
        'header-actions' => [
            'create' => [
                'label' => 'Añadir subtarea',

                'notification' => [
                    'title' => 'Tarea creada',
                    'body'  => 'La tarea se ha creado correctamente.',
                ],
            ],
        ],

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
];
