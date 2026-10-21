<?php

return [
    'navigation' => [
        'title' => 'Ponto de venda',
        'group' => 'Ponto de venda',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Geral',

                'fields' => [
                    'name'             => 'Ponto de venda',
                    'name-placeholder' => 'ex.: Loja NYC',
                    'code'             => 'Código curto',
                    'code-helper-text' => 'Usado como prefixo dos números de pedido e de sessão.',
                    'is-active'        => 'Ativo',
                ],
            ],

            'configurations' => [
                'title' => 'Configurações',

                'tabs' => [
                    'restaurant' => [
                        'title' => 'Modo restaurante',

                        'fields' => [
                            'is-restaurant'                 => 'É um bar/restaurante',
                            'is-restaurant-helper-text'     => 'Ative a gestão de mesas, divisão de contas e tickets de preparo.',
                            'enable-split-bill'             => 'Permitir dividir a conta',
                            'enable-split-bill-helper-text' => 'Dividir o total ou as linhas do pedido.',
                            'enable-print-bill'             => 'Impressão antecipada do recibo',
                            'enable-print-bill-helper-text' => 'Permitir imprimir o recibo antes do pagamento.',
                            'enable-takeaway'               => 'Comer no local / Para viagem',
                            'enable-takeaway-helper-text'   => 'Ajustar a alíquota conforme o cliente come no local ou leva para viagem.',
                            'takeaway-fiscal-position'      => 'Posição fiscal alternativa',
                            'floors'                        => 'Mapa de andares e mesas',
                            'floors-helper-text'            => 'Desenhe os andares e atribua pedidos às mesas.',
                        ],
                    ],

                    'payment' => [
                        'title' => 'Pagamento',

                        'fields' => [
                            'payment-methods'                        => 'Métodos de pagamento',
                            'enable-maximum-difference'              => 'Definir diferença máxima',
                            'enable-maximum-difference-helper-text'  => 'Defina uma diferença máxima permitida entre o valor esperado e o contado durante o fechamento da sessão.',
                            'amount-authorized-diff'                 => 'Diferença máxima',
                            'enable-cash-rounding'                   => 'Arredondamento de dinheiro',
                            'enable-cash-rounding-helper-text'       => 'Defina a menor cédula ou moeda usada para pagar em dinheiro.',
                            'cash-rounding'                          => 'Método de arredondamento',
                            'enable-only-round-cash-method'          => 'Aplicar arredondamento somente em dinheiro',
                            'enable-tip'                             => 'Gorjetas',
                            'enable-tip-helper-text'                 => 'Aceite gorjetas do cliente ou converta o troco dele em gorjeta.',
                            'tip-product'                            => 'Produto de gorjeta',
                        ],
                    ],

                    'interface' => [
                        'title' => 'Interface do PDV',

                        'fields' => [
                            'enable-customer-required'              => 'Cliente obrigatório',
                            'show-product-images'                   => 'Mostrar imagens dos produtos',
                            'show-category-images'                  => 'Mostrar imagens das categorias',
                            'limited-products-amount'               => 'Produtos carregados',
                            'limited-products-amount-helper-text'   => 'Número de produtos carregados no terminal quando uma sessão é aberta.',
                        ],
                    ],

                    'products' => [
                        'title' => 'Produtos e categorias do PDV',

                        'fields' => [
                            'show-margins-and-costs'             => 'Mostrar margens e custos',
                            'show-margins-and-costs-helper-text' => 'Mostrar margens e custos nas informações do produto.',
                            'limit-categories'                   => 'Restringir categorias',
                            'limit-categories-helper-text'       => 'Escolha quais categorias de produtos do PDV ficam disponíveis.',
                            'locked-by-session'                  => 'Bloqueado enquanto houver uma sessão aberta',
                            'categories'                         => 'Categorias de produtos do PDV disponíveis',
                        ],
                    ],

                    'accounting' => [
                        'title' => 'Contabilidade',

                        'fields' => [
                            'journal'                                  => 'Diário de pedidos',
                            'journal-helper-text'                      => 'Diário usado para o lançamento de fechamento da sessão.',
                            'invoice-journal'                          => 'Diário de faturas',
                            'is-closing-entry-by-product'              => 'Lançamento de fechamento por produto',
                            'is-closing-entry-by-product-helper-text'  => 'Exibe o detalhamento das linhas de venda por produto no lançamento de fechamento gerado automaticamente.',
                            'enable-fiscal-position'                   => 'Impostos flexíveis',
                            'enable-fiscal-position-helper-text'       => 'Use posições fiscais para aplicar impostos diferentes por pedido.',
                            'fiscal-position'                          => 'Posição fiscal padrão',
                            'fiscal-positions'                         => 'Posições fiscais',
                            'receivable-account'                       => 'Conta intermediária',
                            'receivable-account-helper-text'           => 'Deixe vazio para usar a conta a receber padrão das configurações do Ponto de venda.',
                            'cash-movement-account'                    => 'Conta de entrada/saída de caixa',
                            'balancing-account'                        => 'Conta de balanceamento',
                            'enable-cogs'                              => 'Custo das mercadorias vendidas',
                            'enable-cogs-helper-text'                  => 'Lançar o custo dos produtos vendidos no fechamento da sessão.',
                            'cogs-journal'                             => 'Diário do custo das mercadorias vendidas',
                            'stock-output-account'                     => 'Conta de saída de estoque',
                        ],
                    ],

                    'pricing' => [
                        'title' => 'Precificação',

                        'fields' => [
                            'tax-display'                            => 'Preços dos produtos',
                            'tax-display-helper-text'                => 'Preços dos produtos exibidos no terminal e nos recibos.',
                            'enable-price-control'                   => 'Controle de preço',
                            'enable-price-control-helper-text'       => 'Restringir a alteração de preços aos gerentes.',
                            'enable-price-list'                      => 'Listas de preços flexíveis',
                            'enable-price-list-helper-text'          => 'Defina vários preços por produto, descontos automáticos etc.',
                            'price-list'                             => 'Lista de preços padrão',
                            'price-list-helper-text'                 => 'Aplicada a todos os pedidos deste caixa. Os operadores só podem trocar de lista quando as listas de preços flexíveis estiverem ativas.',
                            'price-lists'                            => 'Listas de preços disponíveis',
                            'enable-line-discount'                   => 'Descontos por linha',
                            'enable-line-discount-helper-text'       => 'Permitir que os operadores apliquem um desconto por linha.',
                            'enable-global-discount'                 => 'Descontos globais',
                            'enable-global-discount-helper-text'     => 'Adiciona um botão para aplicar um desconto global.',
                            'discount-product'                       => 'Produto de desconto',
                            'global-discount-percentage'             => 'Desconto %',
                            'global-discount-percentage-helper-text' => 'Percentual padrão oferecido ao aplicar um desconto global.',
                        ],
                    ],

                    'receipts' => [
                        'title' => 'Contas e recibos',

                        'fields' => [
                            'enable-receipt-print'                   => 'Impressão de recibos',
                            'enable-receipt-print-helper-text'       => 'Exibe um botão Imprimir na tela do recibo após o pagamento.',
                            'enable-receipt-auto-print'              => 'Impressão automática de recibos',
                            'enable-receipt-auto-print-helper-text'  => 'Imprime os recibos automaticamente assim que o pagamento é registrado.',
                            'receipt-header'                         => 'Cabeçalho do recibo',
                            'receipt-footer'                         => 'Rodapé do recibo',
                            'bills'                                  => 'Moedas/Cédulas',
                            'bills-helper-text'                      => 'Cédulas e moedas oferecidas ao contar o dinheiro na abertura e no fechamento.',
                        ],
                    ],

                    'preparation' => [
                        'title' => 'Preparo',

                        'fields' => [
                            'printers'             => 'Impressoras de preparo',
                            'printers-helper-text' => 'Imprima os pedidos na cozinha, no bar etc.',
                        ],
                    ],

                    'inventory' => [
                        'title' => 'Estoque',

                        'fields' => [
                            'operation-type'                => 'Tipo de operação',
                            'operation-type-helper-text'    => 'Usado para registrar as separações de produtos. Os produtos são consumidos do local de origem padrão dele.',
                            'return-operation-type'         => 'Tipo de operação de devolução',
                            'warehouse'                     => 'Armazém',
                            'enable-ship-later'             => 'Permitir envio posterior',
                            'enable-ship-later-helper-text' => 'Venda produtos e entregue-os depois.',
                            'ship-later-route'              => 'Rota específica',
                            'picking-policy'                => 'Política de envio',
                        ],
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
                    'name'           => 'Ponto de venda',
                    'code'           => 'Código curto',
                    'company-name'   => 'Empresa',
                    'warehouse-name' => 'Armazém',
                    'journal-name'   => 'Diário de pedidos',
                    'is-restaurant'  => 'É um bar/restaurante',
                    'is-active'      => 'Ativo',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'           => 'Nome',
            'closing'        => 'Fechamento',
            'balance'        => 'Saldo',
            'code'           => 'Código',
            'warehouse'      => 'Armazém',
            'operation-type' => 'Tipo de operação',
            'journal'        => 'Diário de vendas',
            'is-restaurant'  => 'Restaurante',
            'is-active'      => 'Ativo',
            'company'        => 'Empresa',
        ],

        'groups' => [
            'warehouse' => 'Armazém',
            'company'   => 'Empresa',
        ],

        'record-actions' => [
            'restore' => [
                'notification' => [
                    'success' => [
                        'title' => 'Ponto de venda restaurado',
                        'body'  => 'O ponto de venda foi restaurado.',
                    ],
                ],
            ],

            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Ponto de venda excluído',
                        'body'  => 'O ponto de venda foi excluído.',
                    ],
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Ponto de venda excluído permanentemente',
                        'body'  => 'O ponto de venda foi excluído permanentemente.',
                    ],

                    'error' => [
                        'title' => 'Não foi possível excluir o ponto de venda',
                        'body'  => 'O ponto de venda não pode ser excluído permanentemente porque possui sessões ou pedidos. Arquive-o em vez disso.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Pontos de venda excluídos permanentemente',
                        'body'  => 'Os pontos de venda selecionados foram excluídos permanentemente.',
                    ],

                    'error' => [
                        'title' => 'Não foi possível excluir os pontos de venda',
                        'body'  => 'Um ou mais pontos de venda não podem ser excluídos permanentemente porque possuem sessões ou pedidos. Arquive-os em vez disso.',
                    ],
                ],
            ],
        ],
    ],
];
