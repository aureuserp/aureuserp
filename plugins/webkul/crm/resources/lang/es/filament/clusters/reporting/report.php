<?php

return [
    'none' => 'Ninguno',

    'chart' => [
        'heading' => ':measure por :group',
    ],

    'filters' => [
        'measure'        => 'Medida',
        'split-by'       => 'Dividir por',
        'no-split'       => 'Sin división',
        'salespeople'    => 'Comerciales',
        'teams'          => 'Equipos de ventas',
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
        'ongoing'           => 'En curso',
        'won'               => 'Ganados',
        'lost'              => 'Perdidos',
    ],

    'table' => [
        'lost' => 'Perdido',

        'columns' => [
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

    'rules' => [
        'active' => [
            'label'         => 'Activos',
            'is-active'     => 'Está activo',
            'is-not-active' => 'No está activo',
            'not-active'    => 'No activos',
        ],

        'won' => [
            'label'      => 'Ganados',
            'is-won'     => 'Está ganado',
            'is-not-won' => 'No está ganado',
            'not-won'    => 'No ganados',
        ],

        'lost' => [
            'label'       => 'Perdidos',
            'is-lost'     => 'Está perdido',
            'is-not-lost' => 'No está perdido',
            'not-lost'    => 'No perdidos',
        ],
    ],

    'tabs' => [
        'leads'         => 'Leads',
        'opportunities' => 'Oportunidades',
        'mine'          => 'Míos',
        'won'           => 'Ganados',
        'lost'          => 'Perdidos',
        'my-activities' => 'Mis actividades',
        'this-month'    => 'Este mes',
    ],
];
