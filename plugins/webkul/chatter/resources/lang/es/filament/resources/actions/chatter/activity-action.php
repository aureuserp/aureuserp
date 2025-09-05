<?php

return [
    'setup' => [
        'title'             => 'Programar Actividad',
        'submit-action-title' => 'Programar',

        'form' => [
            'fields' => [
                'activity-plan' => 'Plan de Actividad',
                'plan-date'     => 'Fecha del Plan',
                'plan-summary'  => 'Resumen del Plan',
                'activity-type' => 'Tipo de Actividad',
                'due-date'      => 'Fecha de Vencimiento',
                'summary'       => 'Resumen',
                'assigned-to'   => 'Asignado a',
                'log-note'      => 'Nota de registro',
            ],
        ],

        'actions' => [
            'notification' => [
                'success' => [
                    'title' => 'Actividad Creada',
                    'body'  => 'La actividad ha sido creada.',
                ],

                'warning'  => [
                    'title' => 'No hay archivos nuevos',
                    'body'  => 'Todos los archivos ya han sido subidos.',
                ],

                'error' => [
                    'title' => 'Error al crear actividad',
                    'body'  => 'No se pudo crear la actividad.',
                ],
            ],
        ],
    ],
];
