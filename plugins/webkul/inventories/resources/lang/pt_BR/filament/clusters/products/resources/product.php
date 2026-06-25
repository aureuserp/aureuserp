<?php

return [
    'navigation' => [
        'title' => 'Produtos',
        'group' => 'Estoque',
    ],

    'form' => [
        'sections' => [
            'inventory' => [
                'title' => 'Estoque',

                'fieldsets' => [
                    'tracking' => [
                        'title' => 'Rastreamento',

                        'fields' => [
                            'track-inventory'              => 'Rastrear estoque',
                            'track-inventory-hint-tooltip' => 'Um produto armazenável exige gestão de estoque.',
                            'track-by'                     => 'Rastrear por',
                            'expiration-date'              => 'Data de validade',
                            'expiration-date-hint-tooltip' => 'Se selecionado, você pode informar datas de validade para o produto e seus lotes/números de série associados.',
                        ],
                    ],

                    'operation' => [
                        'title' => 'Operações',

                        'fields' => [
                            'routes'              => 'Rotas',
                            'routes-hint-tooltip' => 'Com base nos módulos instalados, esta configuração permite definir a rota do produto, como compra, fabricação ou reabastecimento sob demanda.',
                        ],
                    ],

                    'logistics' => [
                        'title' => 'Logística',

                        'fields' => [
                            'responsible'              => 'Responsável',
                            'responsible-hint-tooltip' => 'O prazo de entrega (em dias) representa a duração prometida entre a confirmação do pedido de venda e a entrega do produto.',
                            'weight'                   => 'Peso',
                            'volume'                   => 'Volume',
                            'sale-delay'               => 'Prazo de entrega ao cliente (dias)',
                            'sale-delay-hint-tooltip'  => 'O prazo de entrega (em dias) representa a duração prometida entre a confirmação do pedido de venda e a entrega do produto.',
                        ],
                    ],

                    'traceability' => [
                        'title' => 'Rastreabilidade',

                        'fields' => [
                            'expiration-date'               => 'Data de validade (dias)',
                            'expiration-date-hint-tooltip'  => 'Se selecionado, você pode definir datas de validade para o produto e seus lotes/números de série associados.',
                            'best-before-date'              => 'Data de consumo preferencial (dias)',
                            'best-before-date-hint-tooltip' => 'O número de dias antes da data de validade em que o produto começa a se deteriorar, embora ainda seja seguro para uso. Isso é calculado com base no lote/número de série.',
                            'removal-date'                  => 'Data de remoção (dias)',
                            'removal-date-hint-tooltip'     => 'O número de dias antes da data de validade em que o produto deve ser removido do estoque. Isso é calculado com base no lote/número de série.',
                            'alert-date'                    => 'Data de alerta (dias)',
                            'alert-date-hint-tooltip'       => 'O número de dias antes da data de validade em que um alerta deve ser acionado para o lote/número de série. Isso é calculado com base no lote/número de série.',
                        ],
                    ],
                ],
            ],

            'additional' => [
                'title' => 'Adicional',
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'inventory' => [
                'title' => 'Estoque',

                'entries' => [
                ],

                'fieldsets' => [
                    'tracking' => [
                        'title' => 'Rastreamento',

                        'entries' => [
                            'track-inventory' => 'Rastrear estoque',
                            'track-by'        => 'Rastrear por',
                            'expiration-date' => 'Data de validade',
                        ],
                    ],

                    'operation' => [
                        'title' => 'Operações',

                        'entries' => [
                            'routes' => 'Rotas',
                        ],
                    ],

                    'logistics' => [
                        'title' => 'Logística',

                        'entries' => [
                            'responsible' => 'Responsável',
                            'weight'      => 'Peso',
                            'volume'      => 'Volume',
                            'sale-delay'  => 'Prazo de entrega ao cliente (dias)',
                        ],
                    ],

                    'traceability' => [
                        'title' => 'Rastreabilidade',

                        'entries' => [
                            'expiration-date'  => 'Data de validade (dias)',
                            'best-before-date' => 'Data de consumo preferencial (dias)',
                            'removal-date'     => 'Data de remoção (dias)',
                            'alert-date'       => 'Data de alerta (dias)',
                        ],
                    ],
                ],
            ],
        ],
    ],
];
