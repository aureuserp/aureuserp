<?php

return [
    'setup' => [
        'title'               => 'Programar actividad',
        'submit-action-title' => 'Programar',

        'form' => [
            'fields' => [
                'activity-plan' => 'Plan de actividad',
                'plan-date'     => 'Fecha del plan',
                'plan-summary'  => 'Resumen del plan',
                'activity-type' => 'Tipo de actividad',
                'due-date'      => 'Fecha de vencimiento',
                'summary'       => 'Resumen',
                'assigned-to'   => 'Asignado a',
                'log-note'      => 'Registrar nota',
            ],
        ],

        'actions' => [
            'notification' => [
                'success' => [
                    'title' => 'Actividad creada',
                    'body'  => 'La actividad se ha creado.',
                ],

                'warning'  => [
                    'title' => 'No hay archivos nuevos',
                    'body'  => 'Todos los archivos ya se han subido.',
                ],

                'error' => [
                    'title' => 'Error al crear la actividad',
                    'body'  => 'No se pudo crear la actividad ',
                ],
            ],
        ],
    ],
];
