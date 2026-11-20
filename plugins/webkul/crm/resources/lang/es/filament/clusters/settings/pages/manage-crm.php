<?php

return [
    'title' => 'Gestionar CRM',

    'actions' => [
        'assign-now' => [
            'label'        => 'Asignar ahora',
            'description'  => 'Asigna ahora los leads sin asignar a equipos de ventas y comerciales, según las reglas de asignación.',
            'notification' => 'Se asignaron :teams lead(s) a equipos de ventas y :salespeople a comerciales.',
        ],
    ],

    'form' => [
        'use-leads' => [
            'label'  => 'Calificación de leads',
            'helper' => 'Añadir un paso de calificación antes de crear una oportunidad.',
        ],

        'recurring-revenues' => [
            'label'  => 'Planes de ingresos recurrentes',
            'helper' => 'Definir planes e ingresos recurrentes en las oportunidades.',
        ],

        'multi-teams' => [
            'label'  => 'Vendedores en varios equipos',
            'helper' => 'Asignar comerciales a varios equipos de ventas.',
        ],

        'auto-assignment' => [
            'label'        => 'Asignación automática de leads',
            'helper'       => 'Asigna leads periódicamente según reglas. Todos los equipos de ventas usan este ajuste por defecto, salvo que se indique lo contrario.',
            'running'      => 'Ejecución',
            'manual'       => 'Manualmente',
            'auto'         => 'Repetidamente',
            'repeat-every' => 'Repetir cada',
            'unit'         => 'Unidad',
            'next-run'     => 'Próxima ejecución: :date',

            'units' => [
                'minutes' => 'Minutos',
                'hours'   => 'Horas',
                'days'    => 'Días',
                'weeks'   => 'Semanas',
            ],
        ],
    ],
];
