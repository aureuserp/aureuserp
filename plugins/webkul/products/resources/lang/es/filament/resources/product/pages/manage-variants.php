<?php

return [
    'title' => 'Variantes',

    'form' => [
        'date'                     => 'Fecha',
        'employee'                 => 'Empleado',
        'description'              => 'Descripción',
        'time-spent'               => 'Tiempo Dedicado',
        'time-spent-helper-text' => 'Tiempo dedicado en horas (Ej. 1.5 horas significa 1 hora 30 minutos)',
    ],

    'table' => [
        'columns' => [
            'date'                     => 'Fecha',
            'employee'                 => 'Empleado',
            'description'              => 'Descripción',
            'time-spent'               => 'Tiempo Dedicado',
            'time-spent-on-subtasks' => 'Tiempo Dedicado en Subtareas',
            'total-time-spent'         => 'Tiempo Total Dedicado',
            'remaining-time'           => 'Tiempo Restante',
            'variant-values'           => 'Valores de la Variante',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Variante eliminada',
                    'body'  => 'La variante ha sido eliminada exitosamente.',
                ],
            ],
        ],
    ],
];
