<?php

return [
    'navigation' => [
        'title' => 'Acordos de compra',
        'group' => 'Compras',
    ],

    'global-search' => [
        'vendor' => 'Fornecedor',
        'type' => 'Tipo',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Geral',

                'fields' => [
                    'vendor' => 'Fornecedor',
                    'valid-from' => 'Válido a partir de',
                    'valid-to' => 'Válido até',
                    'buyer' => 'Comprador',
                    'reference' => 'Referência',
                    'reference-placeholder' => 'ex.: PO/123',
                    'agreement-type' => 'Tipo de acordo',
                    'company' => 'Empresa',
                    'currency' => 'Moeda',
                ],
            ],
        ],

        'tabs' => [
            'products' => [
                'title' => 'Produtos',

                'columns' => [
                    'product' => 'Produto',
                    'quantity' => 'Quantidade',
                    'ordered' => 'Pedido',
                    'uom' => 'Unidade de medida',
                    'unit-price' => 'Preço unitário',
                ],

                'fields' => [
                    'product' => 'Produto',
                    'quantity' => 'Quantidade',
                    'ordered' => 'Pedido',
                    'uom' => 'Unidade de medida',
                    'unit-price' => 'Preço unitário',
                ],
            ],

            'additional' => [
                'title' => 'Informações adicionais',
            ],

            'terms' => [
                'title' => 'Termos e condições',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'agreement' => 'Acordo',
            'vendor' => 'Fornecedor',
            'agreement-type' => 'Tipo de acordo',
            'buyer' => 'Comprador',
            'company' => 'Empresa',
            'valid-from' => 'Válido a partir de',
            'valid-to' => 'Válido até',
            'reference' => 'Referência',
            'status' => 'Status',
        ],

        'groups' => [
            'agreement-type' => 'Tipo de acordo',
            'vendor' => 'Fornecedor',
            'state' => 'Estado',
            'created-at' => 'Criado em',
            'updated-at' => 'Atualizado em',
        ],

        'filters' => [
            'agreement' => 'Acordo',
            'vendor' => 'Fornecedor',
            'agreement-type' => 'Tipo de acordo',
            'buyer' => 'Comprador',
            'company' => 'Empresa',
            'valid-from' => 'Válido a partir de',
            'valid-to' => 'Válido até',
            'reference' => 'Referência',
            'status' => 'Status',
            'created-at' => 'Criado em',
            'updated-at' => 'Atualizado em',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Acordo de compra excluído',
                    'body' => 'O acordo de compra foi excluído com sucesso.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Acordo de compra restaurado',
                    'body' => 'O acordo de compra foi restaurado com sucesso.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Acordo de compra excluído permanentemente',
                        'body' => 'O acordo de compra foi excluído permanentemente com sucesso.',
                    ],

                    'error' => [
                        'title' => 'Não foi possível excluir o acordo de compra',
                        'body' => 'O acordo de compra não pode ser excluído porque está em uso.',
                    ],

                    'warning' => [
                        'title' => 'Acordo de compra não pode ser excluído',
                        'body' => 'Somente acordos de compra em estado Rascunho ou Cancelado podem ser excluídos.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Acordos de compra excluídos',
                    'body' => 'Os acordos de compra foram excluídos com sucesso.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Acordos de compra restaurados',
                    'body' => 'Os acordos de compra foram restaurados com sucesso.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Acordos de compra excluídos permanentemente',
                        'body' => 'Os acordos de compra foram excluídos permanentemente com sucesso.',
                    ],

                    'error' => [
                        'title' => 'Não foi possível excluir os acordos de compra',
                        'body' => 'Os acordos de compra não podem ser excluídos porque estão em uso.',
                    ],

                    'warning' => [
                        'title' => 'Acordo de compra não pode ser excluído',
                        'body' => 'Somente acordos de compra em estado Rascunho ou Cancelado podem ser excluídos.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Geral',

                'entries' => [
                    'vendor' => 'Fornecedor',
                    'valid-from' => 'Válido a partir de',
                    'valid-to' => 'Válido até',
                    'buyer' => 'Comprador',
                    'reference' => 'Referência',
                    'reference-placeholder' => 'ex.: PO/123',
                    'agreement-type' => 'Tipo de acordo',
                    'company' => 'Empresa',
                    'currency' => 'Moeda',
                ],
            ],

            'metadata' => [
                'title' => 'Metadados',

                'entries' => [
                    'created-at' => 'Criado em',
                    'created-by' => 'Criado por',
                    'updated-at' => 'Atualizado em',
                ],
            ],
        ],

        'tabs' => [
            'products' => [
                'title' => 'Produtos',

                'entries' => [
                    'product' => 'Produto',
                    'quantity' => 'Quantidade',
                    'ordered' => 'Pedido',
                    'uom' => 'Unidade de medida',
                    'unit-price' => 'Preço unitário',
                ],
            ],

            'additional' => [
                'title' => 'Informações adicionais',
            ],

            'terms' => [
                'title' => 'Termos e condições',
            ],
        ],
    ],
];
