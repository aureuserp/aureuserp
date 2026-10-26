<?php

return [
    'model-label'        => 'Equipe de vendas',
    'plural-model-label' => 'Equipes de vendas',

    'navigation' => [
        'title' => 'Equipes',
    ],

    'table' => [
        'columns' => [
            'name'               => 'Equipe de vendas',
            'open-opportunities' => 'Oportunidades abertas',
            'expected-revenue'   => 'Receita esperada',
            'members'            => 'Membros',
            'company'            => 'Empresa',
        ],

        'filters' => [
            'team-leader' => 'Líder da equipe',
            'created-at'  => 'Criado em',
        ],

        'actions' => [
            'pipeline' => [
                'label' => 'Funil',
            ],
        ],
    ],
];
