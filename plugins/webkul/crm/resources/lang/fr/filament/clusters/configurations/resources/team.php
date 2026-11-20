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
            'title'              => ':team — Membres',
            'inactive'           => 'Inactif',
            'skipped'            => 'Ignore l\'attribution automatique',
            'rules'              => '{1} :count règle|[0,*] :count règles',
            'lead-limit'         => 'Limite de pistes (30 jours) : :assigned / :max',
            'lead-limit-tooltip' => ':assigned sur :max pistes attribuées ces 30 derniers jours',
        ],
    ],
];
