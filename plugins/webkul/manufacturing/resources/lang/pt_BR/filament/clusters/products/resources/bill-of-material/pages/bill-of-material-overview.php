<?php

return [
    'navigation' => [
        'title' => 'Visão geral',
    ],

    'title' => 'Visão geral',

    'heading' => 'Visão geral',

    'header-actions' => [
        'update-product-cost' => [
            'label' => 'Atualizar custo do produto',
            'modal' => [
                'heading'             => 'Atualizar custo pela lista de materiais',
                'description'         => 'Isso atualizará o custo de :product de :current_cost para :new_cost usando o custo unitário da lista de materiais.',
                'submit-action-label' => 'Atualizar custo',
            ],
            'notification' => [
                'success' => [
                    'title' => 'Custo do produto atualizado',
                    'body'  => 'O custo de :product foi atualizado de :old_cost para :new_cost.',
                ],
                'missing-product' => [
                    'title' => 'Produto não encontrado',
                    'body'  => 'Selecione um produto antes de atualizar o custo.',
                ],
            ],
        ],
    ],

    'filters' => [
        'quantity' => 'Quantidade',
        'variant'  => 'Variante',
    ],

    'summary' => [
        'free-to-use' => 'Livre para usar',
        'on-hand'     => 'Em mãos',
        'total-cost'  => 'Custo total',
    ],

    'table' => [
        'columns' => [
            'product'      => 'Produto',
            'quantity'     => 'Quantidade',
            'lead-time'    => 'Prazo de entrega',
            'route'        => 'Rota',
            'bom-cost'     => 'Custo da lista de materiais',
            'product-cost' => 'Custo do produto',
        ],
        'sections' => [
            'operations' => 'Operações',
        ],
        'rows' => [
            'days'    => 'dias',
            'minutes' => 'Minutos',
        ],
        'footer' => [
            'unit-cost' => 'Custo unitário',
        ],
    ],

    'by-products' => [
        'title'   => 'Subprodutos',
        'columns' => [
            'product'  => 'Subproduto',
            'quantity' => 'Quantidade',
            'uom'      => 'Unidade de medida',
        ],
    ],
];
