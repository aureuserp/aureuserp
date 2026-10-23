<?php

return [
    'none' => 'Ninguno',

    'views' => [
        'kanban'  => 'Kanban',
        'graph'   => 'Gráfico',
        'summary' => 'Resumen',
        'list'    => 'Lista',
    ],

    'chart' => [
        'heading' => ':measure por :group',

        'types' => [
            'bar'  => 'Barras',
            'line' => 'Líneas',
            'pie'  => 'Circular',
        ],
    ],

    'filters' => [
        'measure'       => 'Medida',
        'group-by'      => 'Agrupar por',
        'split-by'      => 'Dividir por',
        'no-split'      => 'Sin división',
        'chart-type'    => 'Gráfico',
        'quick-filters' => 'Filtros',
        'salespeople'   => 'Comerciales',
        'teams'         => 'Equipos de ventas',
    ],

    'dimensions' => [
        'salesperson'         => 'Comercial',
        'team'                => 'Equipo de ventas',
        'stage'               => 'Etapa',
        'type'                => 'Tipo',
        'city'                => 'Ciudad',
        'country'             => 'País',
        'lost-reason'         => 'Motivo de pérdida',
        'company'             => 'Empresa',
        'campaign'            => 'Campaña',
        'medium'              => 'Medio',
        'source'              => 'Origen',
        'expected-closing'    => 'Cierre previsto',
        'created-at'          => 'Fecha de creación',
        'converted-at'        => 'Fecha de conversión',
        'closed-at'           => 'Fecha de cierre',
        'activity-type'       => 'Tipo de actividad',
        'assigned-to'         => 'Asignado a',
        'done-by'             => 'Realizado por',
        'done-at'             => 'Fecha de finalización',
        'activity-created-at' => 'Fecha de creación de la actividad',
    ],

    'measures' => [
        'count'            => 'Cantidad',
        'expected-revenue' => 'Ingresos esperados',
        'prorated-revenue' => 'Ingresos prorrateados',
        'probability'      => 'Probabilidad (prom.)',
        'days-to-assign'   => 'Días hasta asignar',
        'days-to-close'    => 'Días hasta cerrar',
    ],

    'presets' => [
        'my-pipeline'       => 'Mi pipeline',
        'unassigned'        => 'Sin asignar',
        'opportunities'     => 'Oportunidades',
        'leads'             => 'Leads',
        'upcoming-closings' => 'Próximos cierres',
        'active'            => 'Activos',
        'ongoing'           => 'En curso',
        'won'               => 'Ganados',
        'lost'              => 'Perdidos',
        'created-this-year' => 'Creados este año',
        'closed-this-year'  => 'Cerrados este año',
        'last-12-months'    => 'Últimos 12 meses',
        'my-activities'     => 'Mis actividades',
        'done-by-me'        => 'Realizadas por mí',
    ],

    'table' => [
        'lost' => 'Perdido',

        'columns' => [
            'group'            => '',
            'name'             => 'Nombre',
            'type'             => 'Tipo',
            'customer'         => 'Cliente',
            'stage'            => 'Etapa',
            'expected-revenue' => 'Ingresos esperados',
            'probability'      => 'Probabilidad',
            'salesperson'      => 'Comercial',
            'team'             => 'Equipo de ventas',
            'expected-closing' => 'Cierre previsto',
            'created-at'       => 'Creado el',
            'closed-at'        => 'Cerrado el',
            'lost-reason'      => 'Motivo de pérdida',
            'country'          => 'País',
            'city'             => 'Ciudad',
            'done-at'          => 'Realizado el',
            'activity-type'    => 'Tipo de actividad',
            'summary'          => 'Resumen',
            'lead'             => 'Lead / Oportunidad',
            'assigned-to'      => 'Asignado a',
            'done-by'          => 'Realizado por',
            'due-date'         => 'Fecha de vencimiento',
        ],
    ],

    'empty-state' => [
        'heading'     => 'Aún no hay datos',
        'description' => 'Pruebe a quitar algunos filtros.',
    ],
];
