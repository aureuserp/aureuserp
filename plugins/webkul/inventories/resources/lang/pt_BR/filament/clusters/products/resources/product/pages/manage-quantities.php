<?php

return [
    'title' => 'Quantidades',

    'tabs' => [
        'internal-locations' => 'Locais internos',
        'transit-locations' => 'Locais de trânsito',
        'on-hand' => 'Em mãos',
        'to-count' => 'A contar',
        'to-apply' => 'A aplicar',
    ],

    'form' => [
        'fields' => [
            'product' => 'Produto',
            'location' => 'Localização',
            'package' => 'Embalagem',
            'lot' => 'Números de lote / série',
            'on-hand-qty' => 'Quantidade em estoque',
            'storage-category' => 'Categoria de armazenamento',
        ],
    ],

    'table' => [
        'columns' => [
            'product' => 'Produto',
            'location' => 'Localização',
            'lot' => 'Números de lote / série',
            'storage-category' => 'Categoria de armazenamento',
            'quantity' => 'Quantidade',
            'package' => 'Embalagem',
            'on-hand' => 'Quantidade em estoque',
            'unit' => 'Unidade',
            'reserved-quantity' => 'Quantidade reservada',

            'on-hand-before-state-updated' => [
                'notification' => [
                    'title' => 'Quantidade atualizada',
                    'body' => 'A quantidade foi atualizada com sucesso.',
                ],
            ],
        ],

        'header-actions' => [
            'create' => [
                'label' => 'Adicionar quantidade',

                'notification' => [
                    'title' => 'Quantidade adicionada',
                    'body' => 'A quantidade foi adicionada com sucesso.',
                ],

                'before' => [
                    'notification' => [
                        'title' => 'Quantidade já existe',
                        'body' => 'Já existe uma quantidade para a mesma configuração. Atualize a quantidade.',
                    ],
                ],
            ],
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Quantidade excluída',
                    'body' => 'A quantidade foi excluída com sucesso.',
                ],
            ],
        ],
    ],
];
