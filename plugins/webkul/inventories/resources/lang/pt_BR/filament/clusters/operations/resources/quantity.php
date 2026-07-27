<?php

return [
    'navigation' => [
        'title' => 'Quantidades',
        'group' => 'Ajustes',
    ],

    'form' => [
        'fields' => [
            'location' => 'Localização',
            'product' => 'Produto',
            'package' => 'Embalagem',
            'lot' => 'Números de lote / série',
            'counted-qty' => 'Quantidade contada',
            'scheduled-at' => 'Agendado em',
            'storage-category' => 'Categoria de armazenamento',
        ],
    ],

    'table' => [
        'columns' => [
            'location' => 'Localização',
            'product' => 'Produto',
            'product-category' => 'Categoria de produto',
            'lot' => 'Números de lote / série',
            'storage-category' => 'Categoria de armazenamento',
            'available-quantity' => 'Quantidade disponível',
            'quantity' => 'Quantidade',
            'package' => 'Embalagem',
            'last-counted-at' => 'Última contagem em',
            'on-hand' => 'Quantidade em estoque',
            'uom' => 'Unidade de medida',
            'counted' => 'Quantidade contada',
            'difference' => 'Diferença',
            'scheduled-at' => 'Agendado em',
            'user' => 'Usuário',
            'company' => 'Empresa',

            'on-hand-before-state-updated' => [
                'notification' => [
                    'title' => 'Quantidade atualizada',
                    'body' => 'A quantidade foi atualizada com sucesso.',
                ],
            ],
        ],

        'groups' => [
            'product' => 'Produto',
            'product-category' => 'Categoria de produto',
            'location' => 'Localização',
            'storage-category' => 'Categoria de armazenamento',
            'lot' => 'Números de lote / série',
            'company' => 'Empresa',
            'package' => 'Embalagem',
        ],

        'filters' => [
            'product' => 'Produto',
            'uom' => 'Unidade de medida',
            'product-category' => 'Categoria de produto',
            'location' => 'Localização',
            'storage-category' => 'Categoria de armazenamento',
            'lot' => 'Números de lote / série',
            'company' => 'Empresa',
            'package' => 'Embalagem',
            'on-hand-quantity' => 'Quantidade em estoque',
            'difference-quantity' => 'Quantidade da diferença',
            'incoming-at' => 'Recebido em',
            'scheduled-at' => 'Agendado em',
            'user' => 'Usuário',
            'created-at' => 'Criado em',
            'updated-at' => 'Atualizado em',
            'company' => 'Empresa',
            'creator' => 'Criador',
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
                        'body' => 'Já existe uma quantidade para esta configuração. Atualize a quantidade existente.',
                    ],
                ],
            ],
        ],

        'actions' => [
            'apply' => [
                'label' => 'Aplicar',

                'notification' => [
                    'title' => 'Alterações de quantidade aplicadas',
                    'body' => 'As alterações de quantidade foram aplicadas com sucesso.',
                ],
            ],

            'clear' => [
                'label' => 'Limpar',

                'notification' => [
                    'title' => 'Alterações de quantidade limpas',
                    'body' => 'As alterações de quantidade foram limpas com sucesso.',
                ],
            ],
        ],
    ],
];
