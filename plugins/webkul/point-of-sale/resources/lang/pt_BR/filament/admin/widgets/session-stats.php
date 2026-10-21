<?php

return [
    'stats' => [
        'open-sessions' => [
            'label'       => 'Sessões abertas',
            'description' => 'Sessões ainda no balcão',
        ],

        'orders' => [
            'label'       => 'Pedidos',
            'description' => 'Pedidos liquidados no período selecionado',
        ],

        'revenue' => [
            'label'       => 'Receita',
            'description' => 'Total dos pedidos liquidados no período selecionado',
        ],

        'failed-operations' => [
            'label'       => 'Operações com falha',
            'description' => 'Pedidos cuja movimentação de estoque precisa de atenção',
        ],
    ],
];
