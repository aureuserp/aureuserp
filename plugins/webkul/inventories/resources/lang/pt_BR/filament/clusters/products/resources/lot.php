<?php

return [
    'navigation' => [
        'title' => 'Lotes/números de série',
        'group' => 'Estoque',
    ],

    'global-search' => [
        'ref' => 'Referência',
        'product' => 'Produto',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Geral',

                'fields' => [
                    'name' => 'Nome',
                    'name-placeholder' => 'ex.: LOT/0001/20121',
                    'product' => 'Produto',
                    'product-hint-tooltip' => 'O produto associado a este lote/número de série. Ele não pode ser alterado se já tiver sido movimentado.',
                    'reference' => 'Referência',
                    'reference-hint-tooltip' => 'Um número de referência interno, se diferente do lote/número de série do fabricante.',
                    'description' => 'Descrição',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name' => 'Nome',
            'product' => 'Produto',
            'on-hand-qty' => 'Quantidade em estoque',
            'reference' => 'Referência interna',
            'created-at' => 'Criado em',
            'updated-at' => 'Atualizado em',
        ],

        'groups' => [
            'product' => 'Produto',
            'location' => 'Localização',
            'created-at' => 'Criado em',
        ],

        'filters' => [
            'product' => 'Produto',
            'location' => 'Localização',
            'creator' => 'Criador',
            'company' => 'Empresa',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Lote excluído',
                        'body' => 'O lote foi excluído com sucesso.',
                    ],

                    'error' => [
                        'title' => 'Não foi possível excluir o lote',
                        'body' => 'O lote não pode ser excluído porque está em uso.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'print' => [
                'label' => 'Imprimir código de barras',
            ],

            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Lotes excluídos',
                        'body' => 'Os lotes foram excluídos com sucesso.',
                    ],

                    'error' => [
                        'title' => 'Não foi possível excluir os lotes',
                        'body' => 'Os lotes não podem ser excluídos porque estão em uso.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Detalhes do lote',

                'entries' => [
                    'name' => 'Nome do lote',
                    'product' => 'Produto',
                    'reference' => 'Referência',
                    'description' => 'Descrição',
                    'on-hand-qty' => 'Quantidade em estoque',
                    'company' => 'Empresa',
                    'created-at' => 'Criado em',
                    'updated-at' => 'Última atualização',
                ],
            ],

            'record-information' => [
                'title' => 'Informações do registro',

                'entries' => [
                    'created-by' => 'Criado por',
                    'created-at' => 'Criado em',
                    'last-updated' => 'Última atualização',
                ],
            ],
        ],
    ],
];
