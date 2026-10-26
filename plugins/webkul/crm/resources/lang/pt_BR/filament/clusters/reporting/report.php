<?php

return [
    'none' => 'Nenhum',

    'views' => [
        'kanban'  => 'Kanban',
        'graph'   => 'Gráfico',
        'summary' => 'Resumo',
        'list'    => 'Lista',
    ],

    'chart' => [
        'heading' => ':measure por :group',

        'types' => [
            'bar'  => 'Barras',
            'line' => 'Linhas',
            'pie'  => 'Pizza',
        ],
    ],

    'filters' => [
        'clear'          => 'Limpar filtros',
        'heading'        => 'Filtros',
        'reset'          => 'Redefinir',
        'measure'        => 'Medida',
        'group-by'       => 'Agrupar por',
        'split-by'       => 'Dividir por',
        'no-split'       => 'Sem divisão',
        'chart-type'     => 'Gráfico',
        'quick-filters'  => 'Filtros',
        'salespeople'    => 'Vendedores',
        'teams'          => 'Equipes de vendas',
    ],

    'dimensions' => [
        'salesperson'         => 'Vendedor',
        'team'                => 'Equipe de vendas',
        'stage'               => 'Etapa',
        'type'                => 'Tipo',
        'city'                => 'Cidade',
        'country'             => 'País',
        'lost-reason'         => 'Motivo de perda',
        'company'             => 'Empresa',
        'campaign'            => 'Campanha',
        'medium'              => 'Meio',
        'source'              => 'Origem',
        'expected-closing'    => 'Fechamento previsto',
        'created-at'          => 'Data de criação',
        'converted-at'        => 'Data de conversão',
        'closed-at'           => 'Data de fechamento',
        'activity-type'       => 'Tipo de atividade',
        'assigned-to'         => 'Atribuído a',
        'done-by'             => 'Concluído por',
        'done-at'             => 'Data de conclusão',
        'activity-created-at' => 'Data de criação da atividade',
    ],

    'measures' => [
        'count'            => 'Quantidade',
        'expected-revenue' => 'Receita esperada',
        'prorated-revenue' => 'Receita proporcional',
        'probability'      => 'Probabilidade (média)',
        'days-to-assign'   => 'Dias para atribuir',
        'days-to-close'    => 'Dias para fechar',
    ],

    'presets' => [
        'my-pipeline'       => 'Meu funil',
        'unassigned'        => 'Não atribuídos',
        'opportunities'     => 'Oportunidades',
        'leads'             => 'Leads',
        'upcoming-closings' => 'Próximos fechamentos',
        'active'            => 'Ativos',
        'ongoing'           => 'Em andamento',
        'won'               => 'Ganhos',
        'lost'              => 'Perdidos',
        'created-this-year' => 'Criados este ano',
        'closed-this-year'  => 'Fechados este ano',
        'last-12-months'    => 'Últimos 12 meses',
        'my-activities'     => 'Minhas atividades',
        'done-by-me'        => 'Concluídas por mim',
    ],

    'table' => [
        'lost' => 'Perdido',

        'columns' => [
            'group'            => '',
            'name'             => 'Nome',
            'type'             => 'Tipo',
            'customer'         => 'Cliente',
            'stage'            => 'Etapa',
            'expected-revenue' => 'Receita esperada',
            'probability'      => 'Probabilidade',
            'salesperson'      => 'Vendedor',
            'team'             => 'Equipe de vendas',
            'expected-closing' => 'Fechamento previsto',
            'created-at'       => 'Criado em',
            'closed-at'        => 'Fechado em',
            'lost-reason'      => 'Motivo de perda',
            'country'          => 'País',
            'city'             => 'Cidade',
            'done-at'          => 'Concluído em',
            'activity-type'    => 'Tipo de atividade',
            'summary'          => 'Resumo',
            'lead'             => 'Lead / Oportunidade',
            'assigned-to'      => 'Atribuído a',
            'done-by'          => 'Concluído por',
            'due-date'         => 'Data de vencimento',
        ],
    ],

    'empty-state' => [
        'heading'     => 'Ainda não há dados',
        'description' => 'Tente remover alguns filtros.',
    ],
];
