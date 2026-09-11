<?php

return [
    'navigation' => [
        'title' => 'Vendas',
    ],

    'navigation-group' => [
        'title' => 'Painel',
    ],

    'filters-form' => [
        'start-date'     => 'Data de Início',
        'end-date'       => 'Data de Fim',
        'salesperson'    => 'Vendedor',
        'country'        => 'País',
        'product'        => 'Produto',
        'customer'       => 'Cliente',
        'category'       => 'Categoria',
        'salesteam'      => 'Equipe de Vendas',
    ],

    'widgets' => [
        'stats-overview' => [
            'heading'          => 'Visão Geral de Vendas',
            'quotation'        => 'Cotação',
            'order'            => 'Pedido',
            'draft'            => 'Cotação Rascunho',
            'cancel'           => 'Cancelar Cotação',
            'total-revenue'    => 'Receita Total',
            'avg-revenue'      => 'Receita Média',
            'fully-invoiced'   => 'Totalmente Faturado',
            'archived'         => 'Arquivado',
            'no-change'        => 'Sem Alteração',
            'increase'         => 'Aumento',
            'decrease'         => 'Diminuição',

            'descriptions' => [
                'quotation'     => 'Cotações enviadas aos clientes',
                'order'         => 'Pedidos confirmados pelos clientes',
                'draft'         => 'Cotações em rascunho',
                'cancel'        => 'Cotação cancelada pelos clientes',
                'total-revenue' => 'Receita total dos pedidos',
                'avg-revenue'   => 'Receita média dos pedidos',
                'fully-invoiced'=> 'Pedidos totalmente faturados',
                'archived'      => 'Pedidos arquivados',
            ],
        ],

        'sales-chart' => [
            'heading'          => 'Gráfico de Vendas',
            'confirmed-orders' => 'Pedidos Confirmados',
            'draft-orders'     => 'Pedidos em Rascunho',
            'sent-orders'      => 'Cotações Enviadas',
            'cancelled-orders' => 'Pedidos Cancelados',
        ],

        'revenue-chart' => [
            'heading' => 'Gráfico de Receita',
            'label'   => 'Receita',
        ],

        'yearly-comparison' => [
            'heading' => 'Comparação Anual de Vendas',
            'label'   => 'Vendas',
        ],

        'top-categories' => [
            'heading' => 'Principais Categorias',
            'column'  => [
                'category'              => 'Categoria',
                'category_full_name'    => 'Nome Completo',
                'product_count'         => 'Quantidade de Produtos',
            ],
        ],

        'top-customers' => [
            'heading' => 'Principais Clientes',
            'column'  => [
                'customer'      => 'Cliente',
                'total_orders'  => 'Total de Pedidos',
                'total_revenue' => 'Receita Total',
            ],
        ],

        'top-products' => [
            'heading' => 'Principais Produtos',
            'column'  => [
                'product'       => 'Produto',
                'qty_sold'      => 'Quantidade Vendida',
                'total_revenue' => 'Receita Total',
            ],
        ],

        'top-sales-teams' => [
            'heading' => 'Principais Equipes de Vendas',
            'column'  => [
                'sales_team'    => 'Equipe de Vendas',
                'total_orders'  => 'Total de Pedidos',
                'total_revenue' => 'Receita',
            ],
        ],

        'top-sales-orders' => [
            'heading' => 'Principais Pedidos de Venda',
            'column'  => [
                'order'         => 'Pedido',
                'customer'      => 'Cliente',
                'order_date'    => 'Data do Pedido',
                'total_amount'  => 'Valor Total',
            ],
        ],

        'top-sales-countries' => [
            'heading' => 'Principais Países de Venda',
            'column'  => [
                'country'        => 'País',
                'total_products' => 'Total de Produtos',
                'total_revenue'  => 'Receita Total',
            ],
        ],
    ],
];
