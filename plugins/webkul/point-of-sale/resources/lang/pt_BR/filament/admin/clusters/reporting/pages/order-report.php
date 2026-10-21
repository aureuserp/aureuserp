<?php

return [
    'navigation' => [
        'title' => 'Pedidos',
    ],

    'title'      => 'Análise de pedidos',

    'filters' => [
        'heading'       => 'Filtros',
        'all-terminals' => 'Todos os terminais',
        'day'           => 'Por dia',
        'week'          => 'Por semana',
        'month'         => 'Por mês',
    ],

    'table' => [
        'heading' => 'Pedidos',
        'total'   => 'Total',
        'empty'   => 'Nenhum pedido neste período.',

        'columns' => [
            'period'  => 'Período',
            'orders'  => 'Pedidos',
            'untaxed' => 'Sem impostos',
            'taxes'   => 'Impostos',
            'total'   => 'Total',
            'margin'  => 'Margem',
        ],
    ],

    'average'    => 'Valor médio do pedido: :amount',
];
