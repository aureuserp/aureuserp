<?php

return [
    'navigation' => [
        'title' => 'Descartes',
        'group' => 'Ajustes',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Geral',

                'fields' => [
                    'product' => 'Produto',
                    'package' => 'Embalagem',
                    'quantity' => 'Quantidade',
                    'unit' => 'Unidade de medida',
                    'lot' => 'Lote/Série',
                    'tags' => 'Tags',
                    'name' => 'Nome',
                    'color' => 'Cor',
                    'owner' => 'Proprietário',
                    'source-location' => 'Local de origem',
                    'destination-location' => 'Local de descarte',
                    'source-document' => 'Documento de origem',
                    'company' => 'Empresa',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'date' => 'Data',
            'reference' => 'Referência',
            'product' => 'Produto',
            'package' => 'Embalagem',
            'quantity' => 'Quantidade',
            'uom' => 'Unidade de medida',
            'source-location' => 'Local de origem',
            'scrap-location' => 'Local de descarte',
            'unit' => 'Unidade de medida',
            'lot' => 'Lote/Série',
            'tags' => 'Tags',
            'state' => 'Estado',
        ],

        'groups' => [
            'product' => 'Produto',
            'source-location' => 'Local de origem',
            'destination-location' => 'Local de descarte',
        ],

        'filters' => [
            'source-location' => 'Local de origem',
            'destination-location' => 'Local de descarte',
            'product' => 'Produto',
            'state' => 'Estado',
            'product-category' => 'Categoria de produto',
            'uom' => 'Unidade de medida',
            'lot' => 'Lote/Série',
            'package' => 'Embalagem',
            'tags' => 'Tags',
            'company' => 'Empresa',
            'quantity' => 'Quantidade',
            'creator' => 'Criador',
            'closed-at' => 'Fechado em',
            'created-at' => 'Criado em',
            'updated-at' => 'Atualizado em',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Descarte excluído',
                        'body' => 'O descarte foi excluído com sucesso.',
                    ],

                    'error' => [
                        'title' => 'Não foi possível excluir o descarte',
                        'body' => 'O descarte não pode ser excluído porque está em uso.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Descartes excluídos',
                        'body' => 'Os descartes selecionados foram excluídos com sucesso.',
                    ],

                    'error' => [
                        'title' => 'Não foi possível excluir os descartes',
                        'body' => 'Os descartes não podem ser excluídos porque estão em uso.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Detalhes do descarte',

                'entries' => [
                    'product' => 'Produto',
                    'quantity' => 'Quantidade',
                    'lot' => 'Lote',
                    'tags' => 'Tags',
                    'package' => 'Embalagem',
                    'owner' => 'Proprietário',
                    'source-location' => 'Local de origem',
                    'destination-location' => 'Local de destino',
                    'source-document' => 'Documento de origem',
                    'company' => 'Empresa',
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
