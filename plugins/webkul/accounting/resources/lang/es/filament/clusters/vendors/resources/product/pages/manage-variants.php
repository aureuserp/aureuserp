<?php

return [
    'title' => 'Variantes',

    'form' => [
        'date'                   => 'Fecha',
        'employee'               => 'Empleado',
        'description'            => 'Descripción',
        'time-spent'             => 'Tiempo dedicado',
        'time-spent-helper-text' => 'Tiempo dedicado en horas (p. ej., 1.5 horas significa 1 hora 30 minutos)',
    ],

    'table' => [
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
            'delete' => [
                'notification' => [
                    'title' => 'Variante eliminada',
                    'body'  => 'La variante se ha eliminado correctamente.',
                ],
            ],
        ],
    ],
];
