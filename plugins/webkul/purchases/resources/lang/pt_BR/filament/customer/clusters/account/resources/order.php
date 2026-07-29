<?php

return [
    'table' => [
        'columns' => [
            'reference' => 'Referência',
            'total-amount' => 'Valor total',
            'confirmation-date' => 'Data de confirmação',
            'status' => 'Status',
        ],
    ],

    'products' => [
        'columns' => [
            'product' => 'Produto',
            'quantity' => 'Quantidade',
            'unit-price' => 'Preço unitário',
            'taxes' => 'Impostos',
            'discount' => 'Desconto %',
            'amount' => 'Valor',
        ],
    ],

    'infolist' => [
        'settings' => [
            'entries' => [
                'buyer' => 'Comprador',
            ],

            'actions' => [
                'accept' => [
                    'label' => 'Aceitar',

                    'notification' => [
                        'title' => 'Cotação aceita',
                        'body' => 'A solicitação de cotação foi confirmada com sucesso.',
                    ],

                    'message' => [
                        'body' => 'A solicitação de cotação foi confirmada pelo fornecedor.',
                    ],
                ],

                'decline' => [
                    'label' => 'Recusar',

                    'notification' => [
                        'title' => 'Cotação recusada',
                        'body' => 'A solicitação de cotação foi recusada com sucesso.',
                    ],

                    'message' => [
                        'body' => 'A solicitação de cotação foi recusada pelo fornecedor.',
                    ],
                ],

                'print' => [
                    'label' => 'Baixar/Imprimir',
                ],
            ],
        ],

        'general' => [
            'entries' => [
                'purchase-order' => 'Pedido de compra nº :id',
                'quotation' => 'Solicitação de cotação nº :id',
                'order-date' => 'Data do pedido',
                'from' => 'De',
                'confirmation-date' => 'Data de confirmação',
                'receipt-date' => 'Data de recebimento',
                'products' => 'Produtos',
                'untaxed-amount' => 'Valor sem impostos',
                'tax-amount' => 'Valor do imposto',
                'total' => 'Total',
                'communication-history' => 'Histórico de comunicação',
            ],
        ],
    ],
];
