<?php

return [
    'model-label'        => 'Equipo de ventas',
    'plural-model-label' => 'Equipos de ventas',

    'navigation' => [
        'title' => 'Equipos de ventas',
    ],

    'pages' => [
        'assignment' => [
            'navigation' => 'Reglas de asignación',
            'title'      => ':team — Reglas de asignación',
        ],

        'alias' => [
            'navigation' => 'Alias de correo',
            'title'      => ':team — Alias de correo',
        ],

        'members' => [
            'title'         => ':team — Miembros',
            'inactive'      => 'Inactivo',
            'skipped'       => 'Omite la asignación automática',
            'rules'         => '{1} :count regla|[0,*] :count reglas',
            'leads-30-days' => 'Leads (30 días)',
            'gauge'         => ':assigned de :max leads asignados en los últimos 30 días',
        ],
    ],
];
