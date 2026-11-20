<?php

return [
    'none' => 'Nenhum',

    'chart' => [
        'heading' => ':measure por :group',
    ],

    'filters' => [
        'measure'        => 'Medida',
        'split-by'       => 'Dividir por',
        'no-split'       => 'Sem divisão',
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
        'ongoing'           => 'Em andamento',
        'won'               => 'Ganhos',
        'lost'              => 'Perdidos',
    ],

    'table' => [
        'lost' => 'Perdido',

        'columns' => [
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

    'rules' => [
        'active' => [
            'label'         => 'Ativos',
            'is-active'     => 'Está ativo',
            'is-not-active' => 'Não está ativo',
            'not-active'    => 'Não ativos',
        ],

        'won' => [
            'label'      => 'Ganhos',
            'is-won'     => 'Está ganho',
            'is-not-won' => 'Não está ganho',
            'not-won'    => 'Não ganhos',
        ],

        'lost' => [
            'label'       => 'Perdidos',
            'is-lost'     => 'Está perdido',
            'is-not-lost' => 'Não está perdido',
            'not-lost'    => 'Não perdidos',
        ],
    ],

    'tabs' => [
        'leads'         => 'Leads',
        'opportunities' => 'Oportunidades',
        'mine'          => 'Meus',
        'won'           => 'Ganhos',
        'lost'          => 'Perdidos',
        'my-activities' => 'Minhas atividades',
        'this-month'    => 'Este mês',
    ],
];
