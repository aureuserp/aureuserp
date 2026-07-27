<?php

return [
    'title' => 'Orçamento',

    'navigation' => [
        'title' => 'Orçamentos',
    ],

    'global-search' => [
        'customer'  => 'Cliente',
        'reference' => 'Referência',
        'amount'    => 'Valor',
    ],

    'form' => [
        'section' => [
            'general' => [
                'title'  => 'Geral',
                'fields' => [
                    'customer'       => 'Cliente',
                    'expiration'     => 'Expiração',
                    'quotation-date' => 'Data do orçamento',
                    'order-date'     => 'Data do pedido',
                    'payment-term'   => 'Condição de pagamento',
                ],
            ],
        ],

        'tabs' => [
            'order-line' => [
                'title' => 'Linha do pedido',

                'repeater' => [
                    'products' => [
                        'title'       => 'Produtos',
                        'add-product' => 'Adicionar produto',

                        'columns'     => [
                            'product'                    => 'Produto',
                            'product-variants'           => 'Variantes do produto',
                            'product-simple'             => 'Produto simples',
                            'quantity'                   => 'Quantidade',
                            'insufficient-stock-tooltip' => 'Estoque insuficiente para atender a esta demanda.',
                            'uom'                        => 'Unidade de medida',
                            'lead-time'                  => 'Prazo de entrega',
                            'qty-delivered'              => 'Entregue',
                            'qty-invoiced'               => 'Faturado',
                            'packaging-qty'              => 'Quantidade por embalagem',
                            'packaging'                  => 'Embalagem',
                            'unit-price'                 => 'Preço unitário',
                            'cost'                       => 'Custo',
                            'margin'                     => 'Margem',
                            'taxes'                      => 'Impostos',
                            'amount'                     => 'Valor',
                            'margin-percentage'          => 'Margem (%)',
                            'discount-percentage'        => 'Desconto (%)',
                        ],

                        'fields'      => [
                            'product'             => 'Produto',
                            'product-variants'    => 'Variantes do produto',
                            'product-simple'      => 'Produto simples',
                            'quantity'            => 'Quantidade',
                            'uom'                 => 'Unidade de medida',
                            'lead-time'           => 'Prazo de entrega',
                            'qty-delivered'       => 'Quantidade entregue',
                            'qty-invoiced'        => 'Quantidade faturada',
                            'packaging-qty'       => 'Quantidade por embalagem',
                            'packaging'           => 'Embalagem',
                            'unit-price'          => 'Preço unitário',
                            'cost'                => 'Custo',
                            'margin'              => 'Margem',
                            'taxes'               => 'Impostos',
                            'amount'              => 'Valor',
                            'margin-percentage'   => 'Margem (%)',
                            'discount-percentage' => 'Desconto (%)',
                        ],

                        'notifications' => [
                            'quantity-below-delivered' => [
                                'title' => 'Não é possível reduzir a quantidade',
                                'body'  => 'Você não pode reduzir a quantidade abaixo da quantidade entregue (:qty).',
                            ],
                        ],

                        'delete-action' => [
                            'error' => [
                                'title' => 'Não é possível excluir o produto',
                                'body'  => 'Produtos não podem ser excluídos de um pedido de venda confirmado.',
                            ],
                        ],

                        'actions' => [
                            'open-product' => [
                                'tooltip' => 'Abrir produto',
                            ],
                        ],
                    ],

                    'product-optional' => [
                        'title'       => 'Produtos opcionais',
                        'add-product' => 'Adicionar produto',

                        'columns' => [
                            'product'             => 'Produto',
                            'description'         => 'Descrição',
                            'quantity'            => 'Quantidade',
                            'uom'                 => 'Unidade de medida',
                            'unit-price'          => 'Preço unitário',
                            'discount-percentage' => 'Desconto (%)',
                        ],

                        'fields'      => [
                            'product'             => 'Produto',
                            'description'         => 'Descrição',
                            'quantity'            => 'Quantidade',
                            'uom'                 => 'Unidade de medida',
                            'unit-price'          => 'Preço unitário',
                            'discount-percentage' => 'Desconto (%)',

                            'actions' => [
                                'tooltip' => [
                                    'add-order-line' => 'Adicionar linha do pedido',
                                ],

                                'notifications' => [
                                    'product-added' => [
                                        'title' => 'Produto adicionado',
                                        'body'  => 'O produto foi adicionado com sucesso.',
                                    ],

                                    'product-not-found' => [
                                        'title' => 'Produto não encontrado',
                                    ],

                                    'product-already-exists' => [
                                        'title' => 'Produto já existe',
                                        'body'  => 'Este produto já está nas linhas do pedido. Atualize a linha existente.',
                                    ],

                                    'missing-product-data' => [
                                        'title' => 'Dados do produto ausentes',
                                        'body'  => 'Não foi possível processar o produto selecionado.',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'other-information' => [
                'title' => 'Outras informações',

                'fieldset' => [
                    'sales' => [
                        'title' => 'Vendas',

                        'fields' => [
                            'sales-person'       => 'Vendedor',
                            'customer-reference' => 'Referência do cliente',
                            'tags'               => 'Tags',
                        ],
                    ],

                    'shipping' => [
                        'title'  => 'Envio',
                        'fields' => [
                            'warehouse'       => 'Armazém',
                            'commitment-date' => 'Data de entrega',
                        ],
                    ],

                    'tracking' => [
                        'title'  => 'Rastreamento',
                        'fields' => [
                            'source-document' => 'Documento de origem',
                            'medium'          => 'Médio',
                            'source'          => 'Origem',
                            'campaign'        => 'Campanha',
                        ],
                    ],

                    'additional-information' => [
                        'title' => 'Informações adicionais',

                        'fields' => [
                            'company'  => 'Empresa',
                            'currency' => 'Moeda',
                        ],
                    ],
                ],
            ],

            'term-and-conditions' => [
                'title' => 'Termos e condições',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'number'             => 'Número',
            'status'             => 'Status',
            'delivery-status'    => 'Status da entrega',
            'invoice-status'     => 'Status da fatura',
            'creation-date'      => 'Data de criação',
            'commitment-date'    => 'Data de compromisso',
            'expected-date'      => 'Data esperada',
            'customer'           => 'Cliente',
            'sales-person'       => 'Vendedor',
            'sales-team'         => 'Equipe de vendas',
            'untaxed-amount'     => 'Valor sem impostos',
            'amount-tax'         => 'Valor do imposto',
            'amount-total'       => 'Valor total',
            'customer-reference' => 'Referência do cliente',
        ],

        'summarizers' => [
            'total'        => 'Total',
            'taxes'        => 'Impostos',
            'total-amount' => 'Valor total',
        ],

        'filters' => [
            'sales-person'     => 'Vendedor',
            'utm-source'       => 'Origem UTM',
            'company'          => 'Empresa',
            'customer'         => 'Cliente',
            'journal'          => 'Diário',
            'invoice-address'  => 'Endereço de faturamento',
            'shipping-address' => 'Endereço de entrega',
            'fiscal-position'  => 'Posição fiscal',
            'payment-term'     => 'Condição de pagamento',
            'currency'         => 'Moeda',
            'created-at'       => 'Criado em',
            'updated-at'       => 'Atualizado em',
        ],

        'groups' => [
            'medium'          => 'Médio',
            'source'          => 'Origem',
            'team'            => 'Equipe',
            'sales-person'    => 'Vendedor',
            'currency'        => 'Moeda',
            'company'         => 'Empresa',
            'customer'        => 'Cliente',
            'quotation-date'  => 'Data do orçamento',
            'commitment-date' => 'Data de compromisso',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Orçamento restaurada',
                    'body'  => 'A orçamento foi restaurada com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Orçamento excluída',
                    'body'  => 'A orçamento foi excluída com sucesso.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Orçamento excluída permanentemente',
                    'body'  => 'A orçamento foi excluída permanentemente com sucesso.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Orçamentos restauradas',
                    'body'  => 'As orçamentos foram restauradas com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Orçamentos excluídas',
                    'body'  => 'As orçamentos foram excluídas com sucesso.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Orçamentos excluídas permanentemente',
                    'body'  => 'As orçamentos foram excluídas permanentemente com sucesso.',
                ],
            ],
        ],

        'empty-state-action' => [
            'create' => [
                'notification' => [
                    'title' => 'Orçamentos criadas',
                    'body'  => 'As orçamentos foram criadas com sucesso.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'section' => [
            'general' => [
                'title'   => 'Geral',
                'entries' => [
                    'sale-order'     => 'Pedido de venda',
                    'customer'       => 'Cliente',
                    'expiration'     => 'Expiração',
                    'quotation-date' => 'Data do orçamento',
                    'payment-term'   => 'Condição de pagamento',
                ],
            ],
        ],

        'tabs' => [
            'order-line' => [
                'title' => 'Linha do pedido',

                'repeater' => [
                    'products' => [
                        'title'       => 'Produtos',
                        'add-product' => 'Adicionar produto',
                        'entries'     => [
                            'product'             => 'Produto',
                            'product-variants'    => 'Variantes do produto',
                            'product-simple'      => 'Produto simples',
                            'quantity'            => 'Quantidade',
                            'qty-delivered'       => 'Entregue',
                            'qty-invoiced'        => 'Faturado',
                            'uom'                 => 'Unidade de medida',
                            'lead-time'           => 'Prazo de entrega',
                            'packaging-qty'       => 'Quantidade por embalagem',
                            'packaging'           => 'Embalagem',
                            'unit-price'          => 'Preço unitário',
                            'cost'                => 'Custo',
                            'margin'              => 'Margem',
                            'taxes'               => 'Impostos',
                            'amount'              => 'Valor',
                            'margin-percentage'   => 'Margem (%)',
                            'discount-percentage' => 'Desconto (%)',
                            'sub-total'           => 'Subtotal',
                        ],
                    ],

                    'product-optional' => [
                        'title'       => 'Produtos opcionais',
                        'add-product' => 'Adicionar produto',
                        'entries'     => [
                            'product'             => 'Produto',
                            'description'         => 'Descrição',
                            'quantity'            => 'Quantidade',
                            'uom'                 => 'Unidade de medida',
                            'unit-price'          => 'Preço unitário',
                            'discount-percentage' => 'Desconto (%)',
                            'sub-total'           => 'Subtotal',

                            'actions' => [
                                'tooltip' => [
                                    'add-order-line' => 'Adicionar linha do pedido',
                                ],

                                'notifications' => [
                                    'product-added' => [
                                        'title' => 'Produto adicionado',
                                        'body'  => 'O produto foi adicionado com sucesso.',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'other-information' => [
                'title' => 'Outras informações',

                'fieldset' => [
                    'sales' => [
                        'title' => 'Vendas',

                        'entries' => [
                            'sales-person'       => 'Vendedor',
                            'customer-reference' => 'Referência do cliente',
                            'tags'               => 'Tags',
                        ],
                    ],

                    'shipping' => [
                        'title'   => 'Envio',
                        'entries' => [
                            'commitment-date' => 'Data de entrega',
                        ],
                    ],

                    'tracking' => [
                        'title'   => 'Rastreamento',
                        'entries' => [
                            'source-document' => 'Documento de origem',
                            'medium'          => 'Médio',
                            'source'          => 'Origem',
                            'campaign'        => 'Campanha',
                        ],
                    ],

                    'additional-information' => [
                        'title' => 'Informações adicionais',

                        'entries' => [
                            'company'  => 'Empresa',
                            'currency' => 'Moeda',
                        ],
                    ],
                ],
            ],

            'term-and-conditions' => [
                'title' => 'Termos e condições',
            ],
        ],
    ],
];
