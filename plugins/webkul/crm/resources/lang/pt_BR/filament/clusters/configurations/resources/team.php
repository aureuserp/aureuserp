<?php

return [
    'model-label'        => 'Equipe de vendas',
    'plural-model-label' => 'Equipes de vendas',

    'navigation' => [
        'title' => 'Equipes de vendas',
    ],

    'pages' => [
        'assignment' => [
            'navigation' => 'Regras de atribuição',
            'title'      => ':team — Regras de atribuição',
        ],

        'alias' => [
            'navigation' => 'Alias de e-mail',
            'title'      => ':team — Alias de e-mail',
        ],

        'members' => [
            'title'              => ':team — Membros',
            'inactive'           => 'Inativo',
            'skipped'            => 'Ignora a atribuição automática',
            'rules'              => '{1} :count regra|[0,*] :count regras',
            'lead-limit'         => 'Limite de leads (30 dias): :assigned / :max',
            'lead-limit-tooltip' => ':assigned de :max leads atribuídos nos últimos 30 dias',
        ],
    ],
];
