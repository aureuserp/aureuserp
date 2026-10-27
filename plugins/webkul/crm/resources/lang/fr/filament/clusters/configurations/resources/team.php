<?php

return [
    'model-label'        => 'Équipe commerciale',
    'plural-model-label' => 'Équipes commerciales',

    'navigation' => [
        'title' => 'Équipes commerciales',
    ],

    'pages' => [
        'assignment' => [
            'navigation' => 'Règles d\'attribution',
            'title'      => ':team — Règles d\'attribution',
        ],

        'alias' => [
            'navigation' => 'Alias e-mail',
            'title'      => ':team — Alias e-mail',
        ],

        'members' => [
            'title'         => ':team — Membres',
            'inactive'      => 'Inactif',
            'skipped'       => 'Ignore l\'attribution automatique',
            'rules'         => '{1} :count règle|[0,*] :count règles',
            'leads-30-days' => 'Pistes (30 jours)',
            'gauge'         => ':assigned sur :max pistes attribuées ces 30 derniers jours',
        ],
    ],
];
