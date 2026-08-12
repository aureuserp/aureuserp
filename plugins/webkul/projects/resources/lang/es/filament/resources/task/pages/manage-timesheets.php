<?php

return [
    'title' => 'Partes de horas',

    'form' => [
        'date'                   => 'Fecha',
        'employee'               => 'Empleado',
        'description'            => 'Descripción',
        'time-spent'             => 'Tiempo dedicado',
        'time-spent-helper-text' => 'Tiempo dedicado en horas (ej. 1.5 horas significa 1 hora 30 minutos)',
    ],

    'table' => [
        'header-actions' => [
            'create' => [
                'label' => 'Añadir parte de horas',

                'notification' => [
                    'title' => 'Parte de horas creado',
                    'body'  => 'El parte de horas se ha creado correctamente.',
                ],
            ],
        ],

        'columns' => [
            'date'                   => 'Fecha',
            'employee'               => 'Empleado',
            'description'            => 'Descripción',
            'time-spent'             => 'Tiempo dedicado',
            'time-spent-on-subtasks' => 'Tiempo dedicado en subtareas',
            'total-time-spent'       => 'Tiempo total dedicado',
            'remaining-time'         => 'Tiempo restante',
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
    ],
];
