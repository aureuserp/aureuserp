<?php

return [
    'global-search' => [
        'vendor' => 'Fornecedor',
        'reference' => 'Referência',
        'amount' => 'Valor',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Geral',

                'fields' => [
                    'vendor' => 'Fornecedor',
                    'vendor-reference' => 'Referência do fornecedor',
                    'vendor-reference-tooltip' => 'O número de referência do pedido de venda ou proposta fornecido pelo fornecedor. Ele é usado para conciliação ao receber produtos, pois essa referência normalmente é incluída na ordem de entrega do fornecedor.',
                    'agreement' => 'Acordo',
                    'currency' => 'Moeda',
                    'confirmation-date' => 'Data de confirmação',
                    'order-deadline' => 'Prazo do pedido',
                    'expected-arrival' => 'Chegada esperada',
                    'confirmed-by-vendor' => 'Confirmado pelo fornecedor',
                    'deliver-to' => 'Entregar em',
                ],
            ],
        ],

        'tabs' => [
            'products' => [
                'title' => 'Produtos',

                'repeater' => [
                    'products' => [
                        'title' => 'Produtos',
                        'add-product-line' => 'Adicionar produto',

                        'fields' => [
                            'product' => 'Produto',
                            'expected-arrival' => 'Chegada esperada',
                            'quantity' => 'Quantidade',
                            'received' => 'Recebido',
                            'billed' => 'Faturado',
                            'unit' => 'Unidade',
                            'packaging-qty' => 'Qtd. por embalagem',
                            'packaging' => 'Embalagem',
                            'taxes' => 'Impostos',
                            'discount-percentage' => 'Desconto (%)',
                            'unit-price' => 'Preço unitário',
                            'amount' => 'Valor',
                        ],

                        'notifications' => [
                            'quantity-below-received' => [
                                'title' => 'Não é possível reduzir a quantidade',
                                'body' => 'Você não pode reduzir a quantidade abaixo da quantidade recebida (:qty).',
                            ],

                            'blanket-order-qty-limit' => [
                                'title' => 'Quantidade excede o limite do pedido em aberto',
                                'body' => 'A quantidade do produto (:product_qty) excede a quantidade disponível (:available_qty) do pedido em aberto.',
                            ],
                        ],

                        'columns' => [
                            'product' => 'Produto',
                            'expected-arrival' => 'Chegada esperada',
                            'quantity' => 'Quantidade',
                            'received' => 'Recebido',
                            'billed' => 'Faturado',
                            'unit' => 'Unidade',
                            'packaging-qty' => 'Qtd. por embalagem',
                            'packaging' => 'Embalagem',
                            'taxes' => 'Impostos',
                            'discount-percentage' => 'Desconto (%)',
                            'unit-price' => 'Preço unitário',
                            'amount' => 'Valor',
                        ],

                        'delete-action' => [
                            'error' => [
                                'title' => 'Não é possível excluir o produto',
                                'body' => 'Produtos não podem ser excluídos de um pedido de compra confirmado.',
                            ],
                        ],

                        'actions' => [
                            'open-product' => [
                                'tooltip' => 'Abrir produto',
                            ],
                        ],
                    ],

                    'section' => [
                        'title' => 'Adicionar seção',

                        'fields' => [],
                    ],

                    'note' => [
                        'title' => 'Adicionar nota',

                        'fields' => [],
                    ],
                ],
            ],

            'additional' => [
                'title' => 'Informações adicionais',

                'fields' => [
                    'buyer' => 'Comprador',
                    'company' => 'Empresa',
                    'source-document' => 'Documento de origem',
                    'incoterm' => 'Incoterm',
                    'incoterm-tooltip' => 'Termos Comerciais Internacionais (Incoterms) são um conjunto de termos comerciais padronizados usados em transações globais para definir responsabilidades entre compradores e vendedores.',
                    'incoterm-location' => 'Local do Incoterm',
                    'payment-term' => 'Condição de pagamento',
                    'fiscal-position' => 'Posição fiscal',
                ],
            ],

            'terms' => [
                'title' => 'Termos e condições',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'favorite' => 'Favorito',
            'priority' => 'Prioridade',
            'vendor-reference' => 'Referência do fornecedor',
            'reference' => 'Referência',
            'vendor' => 'Fornecedor',
            'buyer' => 'Comprador',
            'company' => 'Empresa',
            'order-deadline' => 'Prazo do pedido',
            'source-document' => 'Documento de origem',
            'untaxed-amount' => 'Valor sem impostos',
            'total-amount' => 'Valor total',
            'status' => 'Status',
            'billing-status' => 'Status de faturamento',
            'receipt-status' => 'Status de recebimento',
            'currency' => 'Moeda',
        ],

        'groups' => [
            'vendor' => 'Fornecedor',
            'buyer' => 'Comprador',
            'state' => 'Estado',
            'created-at' => 'Criado em',
            'updated-at' => 'Atualizado em',
        ],

        'filters' => [
            'status' => 'Status',
            'vendor-reference' => 'Referência do fornecedor',
            'reference' => 'Referência',
            'untaxed-amount' => 'Valor sem impostos',
            'total-amount' => 'Valor total',
            'order-deadline' => 'Prazo do pedido',
            'vendor' => 'Fornecedor',
            'buyer' => 'Comprador',
            'company' => 'Empresa',
            'payment-term' => 'Condição de pagamento',
            'incoterm' => 'Incoterm',
            'status' => 'Status',
            'created-at' => 'Criado em',
            'updated-at' => 'Atualizado em',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Pedido excluído',
                        'body' => 'O pedido foi excluído com sucesso.',
                    ],

                    'error' => [
                        'title' => 'Não foi possível excluir o pedido',
                        'body' => 'O pedido não pode ser excluído porque está em uso.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Pedidos excluídos',
                        'body' => 'Os pedidos foram excluídos com sucesso.',
                    ],

                    'error' => [
                        'title' => 'Não foi possível excluir os pedidos',
                        'body' => 'Os pedidos não podem ser excluídos porque estão em uso.',
                    ],
                ],
            ],
        ],
    ],

    'summary' => [
        'tax' => 'Imposto',
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Geral',

                'entries' => [
                    'purchase-order' => 'Pedido de compra',
                    'vendor' => 'Fornecedor',
                    'vendor-reference' => 'Referência do fornecedor',
                    'vendor-reference-tooltip' => 'O número de referência do pedido de venda ou proposta fornecido pelo fornecedor. Ele é usado para conciliação ao receber produtos, pois essa referência normalmente é incluída na ordem de entrega do fornecedor.',
                    'agreement' => 'Acordo',
                    'currency' => 'Moeda',
                    'confirmation-date' => 'Data de confirmação',
                    'order-deadline' => 'Prazo do pedido',
                    'expected-arrival' => 'Chegada esperada',
                    'confirmed-by-vendor' => 'Confirmado pelo fornecedor',
                ],
            ],
        ],

        'tabs' => [
            'products' => [
                'title' => 'Produtos',

                'repeater' => [
                    'products' => [
                        'title' => 'Produtos',
                        'add-product-line' => 'Adicionar produto',

                        'entries' => [
                            'product' => 'Produto',
                            'expected-arrival' => 'Chegada esperada',
                            'quantity' => 'Quantidade',
                            'received' => 'Recebido',
                            'billed' => 'Faturado',
                            'unit' => 'Unidade',
                            'packaging-qty' => 'Qtd. por embalagem',
                            'packaging' => 'Embalagem',
                            'taxes' => 'Impostos',
                            'discount-percentage' => 'Desconto (%)',
                            'unit-price' => 'Preço unitário',
                            'amount' => 'Valor',
                        ],
                    ],

                    'section' => [
                        'title' => 'Adicionar seção',
                    ],

                    'note' => [
                        'title' => 'Adicionar nota',
                    ],
                ],
            ],

            'additional' => [
                'title' => 'Informações adicionais',

                'entries' => [
                    'buyer' => 'Comprador',
                    'company' => 'Empresa',
                    'source-document' => 'Documento de origem',
                    'incoterm' => 'Incoterm',
                    'incoterm-tooltip' => 'Termos Comerciais Internacionais (Incoterms) são um conjunto de termos comerciais padronizados usados em transações globais para definir responsabilidades entre compradores e vendedores.',
                    'incoterm-location' => 'Local do Incoterm',
                    'payment-term' => 'Condição de pagamento',
                    'fiscal-position' => 'Posição fiscal',
                ],
            ],

            'terms' => [
                'title' => 'Termos e condições',
            ],
        ],
    ],
];
