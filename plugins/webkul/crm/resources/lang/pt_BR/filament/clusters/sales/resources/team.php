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

    'grid' => [
        'new-opportunities'  => '{1} :count nova oportunidade|[0,*] :count novas oportunidades',
        'quotations'         => '{1} :count cotação|[0,*] :count cotações',
        'configuration'      => 'Configuração',
        'menu'               => 'Menu da equipe',
        'open-opportunities' => '{1} :count oportunidade aberta|[0,*] :count oportunidades abertas',
        'orders-to-invoice'  => '{1} :count pedido a faturar|[0,*] :count pedidos a faturar',
        'chart'              => 'Novas oportunidades por semana',
        'target-progress'    => 'Faturado este mês: :invoiced de :target',
    ],

    'grid-menu' => [
        'view'          => 'Visualizar',
        'reporting'     => 'Relatórios',
        'leads'         => 'Leads',
        'opportunities' => 'Oportunidades',
        'quotations'    => 'Cotações',
        'sales-orders'  => 'Pedidos de venda',
        'forecast'      => 'Previsão',
        'activities'    => 'Atividades',
        'no-color'      => 'Sem cor',
    ],
];
