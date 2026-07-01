<?php

return [
    'title' => 'Moedas',

    'navigation' => [
        'title' => 'Moedas',
        'group' => 'Configurações',
    ],

    'form' => [
        'sections' => [
            'currency-details' => [
                'title' => 'Informações da moeda',

                'fields' => [
                    'name'         => 'Nome da moeda',
                    'name-tooltip' => 'Informe o nome oficial da moeda',
                    'symbol'       => 'Símbolo da moeda',
                    'full-name'    => 'Nome completo',
                    'iso-numeric'  => 'Código numérico ISO',
                ],
            ],

            'format-information' => [
                'title' => 'Configuração de formato',

                'fields' => [
                    'decimal-places'       => 'Casas decimais',
                    'rounding'             => 'Precisão de arredondamento',
                    'rounding-helper-text' => 'Defina a precisão de arredondamento para cálculos de moeda',
                ],
            ],

            'status-and-configuration-information' => [
                'title' => 'Status e configuração',

                'fields' => [
                    'status' => 'Status',
                ],
            ],

            'rates' => [
                'title'       => 'Taxas de câmbio',
                'description' => 'Gerencie taxas de câmbio históricas desta moeda em relação à moeda base.',

                'fields' => [
                    'name'              => 'Data',
                    'unit-per-currency' => 'Unidade por :currency',
                    'currency-per-unit' => ':currency por unidade',
                ],

                'add-rate'   => 'Adicionar taxa',
                'item-label' => 'Taxa',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'           => 'Nome da moeda',
            'symbol'         => 'Símbolo',
            'full-name'      => 'Nome completo',
            'iso-numeric'    => 'Código ISO',
            'decimal-places' => 'Casas decimais',
            'rounding'       => 'Arredondamento',
            'status'         => 'Status',
            'created-at'     => 'Criado em',
            'updated-at'     => 'Atualizado em',
        ],

        'groups' => [
            'name'           => 'Nome',
            'status'         => 'Status',
            'decimal-places' => 'Casas decimais',
            'creation-date'  => 'Data de criação',
            'last-update'    => 'Última atualização',
        ],

        'filters' => [
            'status' => 'Status',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Moeda excluída',
                    'body'  => 'A moeda foi excluída com sucesso.',

                    'success' => [
                        'title' => 'Moeda excluída',
                        'body'  => 'A moeda foi excluída com sucesso.',
                    ],

                    'error' => [
                        'title' => 'Moeda não pôde ser excluída',
                        'body'  => 'A moeda não pode ser excluída porque está em uso no momento.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Moedas excluídas',
                    'body'  => 'As moedas foram excluídas com sucesso.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'currency-details' => [
                'title' => 'Informações da moeda',

                'entries' => [
                    'name'        => 'Nome da moeda',
                    'symbol'      => 'Símbolo da moeda',
                    'full-name'   => 'Nome completo',
                    'iso-numeric' => 'Código numérico ISO',
                ],
            ],

            'format-information' => [
                'title' => 'Configuração de formato',

                'entries' => [
                    'decimal-places' => 'Casas decimais',
                    'rounding'       => 'Precisão de arredondamento',
                ],
            ],

            'status-and-configuration-information' => [
                'title' => 'Status e configuração',

                'entries' => [
                    'status' => 'Status',
                ],
            ],

            'rates' => [
                'title' => 'Taxas de câmbio',

                'entries' => [
                    'name'              => 'Data',
                    'unit-per-currency' => 'Unidade por :currency',
                    'currency-per-unit' => ':currency por unidade',
                ],
            ],
        ],
    ],
];
