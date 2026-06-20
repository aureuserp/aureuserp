<?php

return [
    'title' => 'Modelo de cotação',

    'navigation' => [
        'title' => 'Modelo de cotação',
        'group' => 'Pedidos de venda',
    ],

    'form' => [
        'tabs' => [
            'products' => [
                'title' => 'Produtos',
                'fields' => [
                    'products' => 'Produtos',
                    'name' => 'Nome',
                    'quantity' => 'Quantidade',
                ],
            ],

            'terms-and-conditions' => [
                'title' => 'Termos e condições',
                'fields' => [
                    'note-placeholder' => 'Escreva seus termos e condições para as cotações.',
                ],
            ],
        ],

        'sections' => [
            'general' => [
                'title' => 'Informações gerais',

                'fields' => [
                    'name' => 'Nome',
                    'quotation-validity' => 'Validade da cotação',
                    'sale-journal' => 'Diário de vendas',
                ],
            ],

            'signature-and-payment' => [
                'title' => 'Assinatura e pagamentos',

                'fields' => [
                    'online-signature' => 'Assinatura online',
                    'online-payment' => 'Pagamento online',
                    'prepayment-percentage' => 'Percentual de pré-pagamento',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'created-by' => 'Criado por',
            'company' => 'Empresa',
            'name' => 'Nome',
            'number-of-days' => 'Número de dias',
            'journal' => 'Diário de vendas',
            'signature-required' => 'Assinatura obrigatória',
            'payment-required' => 'Pagamento obrigatório',
            'prepayment-percentage' => 'Percentual de pré-pagamento',
        ],
        'groups'  => [
            'company' => 'Empresa',
            'name' => 'Nome',
            'journal' => 'Diário',
        ],
        'filters' => [
            'created-by' => 'Criado por',
            'company' => 'Empresa',
            'name' => 'Nome',
            'created-at' => 'Criado em',
            'updated-at' => 'Atualizado em',
        ],
        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Modelo de cotação excluído',
                    'body' => 'O modelo de cotação foi excluído com sucesso.',
                ],
            ],

        ],
        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Modelo de cotação excluído',
                    'body' => 'O modelo de cotação foi excluído com sucesso.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'tabs' => [
            'products' => [
                'title' => 'Produtos',
            ],
            'terms-and-conditions' => [
                'title' => 'Termos e condições',
            ],
        ],
        'sections' => [
            'general' => [
                'title' => 'Informações gerais',
            ],
            'signature_and_payment' => [
                'title' => 'Assinatura e pagamento',
            ],
        ],
        'entries' => [
            'product' => 'Produto',
            'description' => 'Descrição',
            'quantity' => 'Quantidade',
            'unit-price' => 'Preço unitário',
            'section-name' => 'Nome da seção',
            'note-title' => 'Título da nota',
            'name' => 'Nome do modelo',
            'quotation-validity' => 'Validade da cotação',
            'sale-journal' => 'Diário de vendas',
            'online-signature' => 'Assinatura online',
            'online-payment' => 'Pagamento online',
            'prepayment-percentage' => 'Percentual de pré-pagamento',
        ],
    ],
];
