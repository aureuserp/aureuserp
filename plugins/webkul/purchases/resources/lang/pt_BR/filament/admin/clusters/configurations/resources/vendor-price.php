<?php

return [
    'navigation' => [
        'title' => 'Listas de preços de fornecedores',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Geral',

                'fields' => [
                    'vendor' => 'Fornecedor',
                    'vendor-product-name' => 'Nome do produto no fornecedor',
                    'vendor-product-name-tooltip' => 'O nome do produto no fornecedor aparecerá na solicitação de cotação. Deixe em branco para usar o nome interno do produto.',
                    'vendor-product-code' => 'Código do produto no fornecedor',
                    'vendor-product-code-tooltip' => 'O código do produto no fornecedor aparecerá na solicitação de cotação. Deixe em branco para usar o código interno.',
                    'delay' => 'Prazo de entrega (dias)',
                    'delay-tooltip' => 'O prazo (em dias) entre a confirmação do pedido de compra e o recebimento do produto no armazém. Usado pelo agendador para o planejamento automático de pedidos de compra.',
                ],
            ],

            'prices' => [
                'title' => 'Preços',

                'fields' => [
                    'product' => 'Produto',
                    'quantity' => 'Quantidade',
                    'quantity-tooltip' => 'A quantidade mínima necessária para comprar deste fornecedor e se qualificar para o preço especificado. Ela é expressa na unidade de medida do produto do fornecedor ou, se não definida, na unidade de medida padrão do produto.',
                    'unit-price' => 'Preço unitário',
                    'unit-price-tooltip' => 'O preço por unidade deste produto no fornecedor, expresso na unidade de medida do produto do fornecedor ou, se não definida, na unidade de medida padrão do produto.',
                    'currency' => 'Moeda',
                    'valid-from' => 'Válido a partir de',
                    'valid-to' => 'Válido até',
                    'discount' => 'Desconto (%)',
                    'company' => 'Empresa',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'vendor' => 'Fornecedor',
            'vendor-product-name' => 'Nome do produto no fornecedor',
            'vendor-product-code' => 'Código do produto no fornecedor',
            'delay' => 'Prazo de entrega (dias)',
            'product' => 'Produto',
            'quantity' => 'Quantidade',
            'unit-price' => 'Preço unitário',
            'currency' => 'Moeda',
            'valid-from' => 'Válido a partir de',
            'valid-to' => 'Válido até',
            'discount' => 'Desconto (%)',
            'company' => 'Empresa',
            'created-at' => 'Criado em',
            'updated-at' => 'Atualizado em',
        ],

        'filters' => [
            'vendor' => 'Filtrar por fornecedor',
            'product' => 'Filtrar por produto',
            'currency' => 'Filtrar por moeda',
            'company' => 'Filtrar por empresa',
            'price-from' => 'Preço mínimo',
            'price-to' => 'Preço máximo',
            'min-qty-from' => 'Quantidade mínima de',
            'min-qty-to' => 'Quantidade mínima até',
            'starts-from' => 'Data de início da validade',
            'ends-before' => 'Data de fim da validade',
            'created-from' => 'Criado a partir de',
            'created-until' => 'Criado até',
        ],

        'groups' => [
            'vendor' => 'Fornecedor',
            'product' => 'Produto',
            'created-at' => 'Criado em',
            'updated-at' => 'Atualizado em',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Preço do fornecedor excluído',
                        'body' => 'O preço do fornecedor foi excluído com sucesso.',
                    ],

                    'error' => [
                        'title' => 'Não foi possível excluir o preço do fornecedor',
                        'body' => 'O preço do fornecedor não pode ser excluído porque está em uso.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Preços de fornecedores excluídos',
                        'body' => 'Os preços de fornecedores foram excluídos com sucesso.',
                    ],

                    'error' => [
                        'title' => 'Não foi possível excluir os preços de fornecedores',
                        'body' => 'Os preços de fornecedores não podem ser excluídos porque estão em uso.',
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
                    'vendor-product-name' => 'Nome do produto no fornecedor',
                    'vendor-product-name-tooltip' => 'O nome do produto no fornecedor aparecerá na solicitação de cotação. Deixe em branco para usar o nome interno do produto.',
                    'vendor-product-code' => 'Código do produto no fornecedor',
                    'vendor-product-code-tooltip' => 'O código do produto no fornecedor aparecerá na solicitação de cotação. Deixe em branco para usar o código interno.',
                    'delay' => 'Prazo de entrega (dias)',
                    'delay-tooltip' => 'O prazo (em dias) entre a confirmação do pedido de compra e o recebimento do produto no armazém. Usado pelo agendador para o planejamento automático de pedidos de compra.',
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

            'prices' => [
                'title' => 'Preços',

                'entries' => [
                    'product' => 'Produto',
                    'quantity' => 'Quantidade',
                    'quantity-tooltip' => 'A quantidade mínima necessária para comprar deste fornecedor e se qualificar para o preço especificado. Ela é expressa na unidade de medida do produto do fornecedor ou, se não definida, na unidade de medida padrão do produto.',
                    'unit-price' => 'Preço unitário',
                    'unit-price-tooltip' => 'O preço por unidade deste produto no fornecedor, expresso na unidade de medida do produto do fornecedor ou, se não definida, na unidade de medida padrão do produto.',
                    'currency' => 'Moeda',
                    'valid-from' => 'Válido a partir de',
                    'valid-to' => 'Válido até',
                    'discount' => 'Desconto (%)',
                    'company' => 'Empresa',
                ],
            ],
        ],
    ],
];
