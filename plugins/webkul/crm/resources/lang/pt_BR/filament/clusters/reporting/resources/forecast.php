<?php

return [
    'title' => 'Previsão',

    'navigation' => [
        'title' => 'Previsão',
    ],

    'kanban' => [
        'filters' => [
            'salesperson' => 'Vendedor',
            'team'        => 'Equipe de vendas',
            'period'      => 'Fechamento previsto',
            'upcoming'    => 'Próximos fechamentos',
            'all-dates'   => 'Qualquer data',
            'presets'     => 'Filtros',
            'all'         => 'Todos',
        ],

        'empty-state' => [
            'heading'     => 'Nenhuma oportunidade para prever',
            'description' => 'Defina uma data de fechamento prevista nas suas oportunidades para vê-las aqui.',
        ],

        'notifications' => [
            'moved'        => 'Data de fechamento prevista atualizada',
            'unauthorized' => 'Você não tem permissão para alterar esta oportunidade.',
        ],
    ],
];
