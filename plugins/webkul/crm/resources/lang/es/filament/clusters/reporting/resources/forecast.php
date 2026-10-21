<?php

return [
    'title' => 'Previsión',

    'navigation' => [
        'title' => 'Previsión',
    ],

    'kanban' => [
        'filters' => [
            'salesperson' => 'Comercial',
            'team'        => 'Equipo de ventas',
            'period'      => 'Cierre previsto',
            'upcoming'    => 'Próximos cierres',
            'all-dates'   => 'Cualquier fecha',
            'all'         => 'Todos',
        ],

        'empty-state' => [
            'heading'     => 'No hay oportunidades para prever',
            'description' => 'Defina una fecha de cierre prevista en sus oportunidades para verlas aquí.',
        ],

        'notifications' => [
            'moved'        => 'Fecha de cierre prevista actualizada',
            'unauthorized' => 'No tiene permiso para modificar esta oportunidad.',
        ],
    ],
];
