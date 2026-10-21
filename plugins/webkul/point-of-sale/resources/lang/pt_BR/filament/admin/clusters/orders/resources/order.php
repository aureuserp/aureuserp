<?php

return [

    'form' => [
        'section' => [
            'general' => [
                'title'  => 'Geral',
                'fields' => [
                    'order'      => 'Pedido do ponto de venda',
                    'ordered-at' => 'Data',
                    'customer'   => 'Cliente',
                    'cashier'    => 'Operador de caixa',
                ],
            ],
        ],

        'tabs' => [
            'products' => [
                'title'   => 'Produtos',
                'columns' => [
                    'product'            => 'Produto',
                    'lot'                => 'Número de lote/série',
                    'quantity'           => 'Quantidade',
                    'uom'                => 'UdM',
                    'unit-price'         => 'Preço unitário',
                    'discount'           => 'Desc.%',
                    'taxes'              => 'Impostos',
                    'tax-excluded'       => 'Sem impostos',
                    'tax-included'       => 'Com impostos',
                    'full-product-name'  => 'Nome completo do produto',
                    'customer-note'      => 'Observação do cliente',
                    'total-cost'         => 'Custo total',
                    'margin'             => 'Margem',
                    'margin-percent'     => 'Margem (%)',
                    'refunded-quantity'  => 'Quantidade reembolsada',
                ],

                'actions' => [
                    'open-product' => 'Abrir produto',
                ],

                'summary' => [
                    'untaxed'  => 'Valor sem impostos',
                    'taxes'    => 'Impostos',
                    'rounding' => 'Arredondamento',
                    'total'    => 'Total',
                    'paid'     => 'Pago',
                    'change'   => 'Troco',
                ],
            ],

            'payments' => [
                'title'  => 'Pagamentos',
                'add'    => 'Adicionar uma linha',
                'fields' => [
                    'paid-at'         => 'Data',
                    'method'          => 'Método de pagamento',
                    'amount'          => 'Valor',
                    'card-type'       => 'Número do cartão (últimos 4 dígitos)',
                    'card-brand'      => 'Bandeira do cartão',
                    'cardholder-name' => 'Nome do titular',
                ],
            ],

            'extra-info' => [
                'title'  => 'Informações extras',
                'fields' => [
                    'receipt-number'  => 'Número do recibo',
                    'tracking-number' => 'Número de rastreio',
                    'email'           => 'E-mail',
                    'mobile'          => 'Celular',
                ],
            ],

            'notes' => [
                'title' => 'Observações gerais',
            ],
        ],
    ],

    'infolist' => [
        'section' => [
            'general' => [
                'title'   => 'Geral',
                'entries' => [
                    'order'         => 'Pedido do ponto de venda',
                    'customer'      => 'Cliente',
                    'session'       => 'Sessão',
                    'ordered-at'    => 'Pedido em',
                    'point-of-sale' => 'Ponto de venda',
                ],
            ],
        ],

        'tabs' => [
            'order-line' => [
                'title'   => 'Linha do pedido',
                'entries' => [
                    'product'    => 'Produto',
                    'quantity'   => 'Quantidade',
                    'unit-price' => 'Preço unitário',
                    'taxes'      => 'Impostos',
                    'discount'   => 'Desconto (%)',
                    'amount'     => 'Valor',
                ],
                'totals' => [
                    'untaxed' => 'Valor sem impostos',
                    'taxes'   => 'Impostos',
                    'total'   => 'Valor total',
                    'margin'  => 'Margem',
                ],
            ],

            'payments' => [
                'title'   => 'Pagamentos',
                'entries' => [
                    'method'  => 'Método de pagamento',
                    'amount'  => 'Valor',
                    'paid-at' => 'Pago em',
                ],
            ],

            'extra-info' => [
                'title'   => 'Informações extras',
                'entries' => [
                    'receipt-number'  => 'Número do recibo',
                    'tracking-number' => 'Número de rastreio',
                    'email'           => 'E-mail',
                    'mobile'          => 'Celular',
                ],
            ],

            'other-information' => [
                'title'   => 'Outras informações',
                'entries' => [
                    'reference'      => 'Referência',
                    'receipt-number' => 'Número do recibo',
                    'operation'      => 'Operação',
                    'cashier'        => 'Operador de caixa',
                    'paid'           => 'Pago',
                    'change'         => 'Troco',
                ],
            ],
        ],
    ],
    'navigation' => [
        'title' => 'Pedidos',
        'group' => 'Ponto de venda',
    ],

    'table' => [
        'columns' => [
            'name'                 => 'Ref. do pedido',
            'reference'            => 'Número do recibo',
            'session'              => 'Sessão',
            'config'               => 'Ponto de venda',
            'partner'              => 'Cliente',
            'ordered-at'           => 'Data',
            'amount-total'         => 'Total',
            'amount-paid'          => 'Pago',
            'user'                 => 'Operador de caixa',
            'has-failed-operation' => 'Operação com falha',
            'is-invoiced'          => 'Faturado',
            'is-edited'            => 'Editado',
            'tracking-number'      => 'Número de rastreio',
            'state'                => 'Status',
        ],

        'groups' => [
            'session'    => 'Sessão',
            'config'     => 'Ponto de venda',
            'state'      => 'Status',
            'ordered-at' => 'Pedido em',
        ],

        'filters' => [
            'state'   => 'Status',
            'session' => 'Sessão',
            'config'  => 'Ponto de venda',
        ],
    ],
];
