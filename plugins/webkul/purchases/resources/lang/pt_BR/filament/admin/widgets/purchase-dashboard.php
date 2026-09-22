<?php

return [
    'unknown'      => 'Desconhecido',
    'other'        => 'Outros',
    'share'        => 'Participação',
    'status'       => 'Situação',
    'stats'        => [
        'heading'              => 'Métricas Principais',
        'draft-rfqs'           => 'Cotações em Rascunho',
        'sent-rfqs'            => 'Cotações Enviadas',
        'confirmed-orders'     => 'Pedidos Confirmados',
        'total-purchase-value' => 'Valor Total de Compra',
        'increase'             => ':percent% de aumento',
        'decrease'             => ':percent% de redução',
        'no-change'            => 'Sem alteração',
    ],

    'trend'        => [
        'heading' => 'Tendência de Pedidos de Compra por Situação',
        'states'  => [
            'draft'    => 'Rascunho',
            'sent'     => 'Enviado',
            'purchase' => 'Compra',
            'done'     => 'Concluído',
            'canceled' => 'Cancelado',
        ],
    ],

    'vendor-spend' => [
        'heading'         => 'Participação de Gastos por Fornecedor',
        'total-purchased' => 'Total Comprado',
    ],

    'top-orders'   => [
        'heading' => 'Principais Pedidos',
        'columns' => [
            'reference'  => 'Número do Pedido',
            'vendor'     => 'Fornecedor',
            'ordered-at' => 'Data do Pedido',
            'amount'     => 'Valor Total',
        ],
    ],

    'top-products' => [
        'heading' => 'Produtos Mais Comprados',
        'columns' => [
            'name'     => 'Produto',
            'quantity' => 'Quantidade Total',
            'value'    => 'Valor Total',
        ],
    ],
];
