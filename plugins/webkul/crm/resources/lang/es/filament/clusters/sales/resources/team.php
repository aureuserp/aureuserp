<?php

return [
    'model-label'        => 'Equipo de ventas',
    'plural-model-label' => 'Equipos de ventas',

    'navigation' => [
        'title' => 'Equipos',
    ],

    'table' => [
        'columns' => [
            'name'               => 'Equipo de ventas',
            'open-opportunities' => 'Oportunidades abiertas',
            'expected-revenue'   => 'Ingresos esperados',
            'members'            => 'Miembros',
            'company'            => 'Empresa',
        ],

        'filters' => [
            'team-leader' => 'Líder del equipo',
            'created-at'  => 'Creado el',
        ],

        'actions' => [
            'pipeline' => [
                'label' => 'Pipeline',
            ],
        ],
    ],
];
