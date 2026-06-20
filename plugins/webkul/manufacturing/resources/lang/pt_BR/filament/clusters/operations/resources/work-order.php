<?php

return [
    'navigation' => [
        'title' => 'Ordens de trabalho',
        'group' => 'Operações',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Geral',
                'fields' => [
                    'work-order' => 'Ordem de trabalho',
                    'work-center' => 'Centro de trabalho',
                    'product' => 'Produto',
                    'quantity' => 'Quantidade',
                    'manufacturing-order' => 'Ordem de produção',
                    'lot-serial' => 'Número de lote/série',
                    'start-date' => 'Data de início',
                    'end-date' => 'Data de término',
                    'date-range-separator' => 'até',
                    'expected-duration' => 'Duração esperada',
                    'duration-suffix' => 'minutos',
                    'real-duration' => 'Duração real',
                ],
            ],
        ],
        'tabs' => [
            'time-tracking' => [
                'title' => 'Controle de tempo',
                'add-action' => 'Adicionar uma linha',
                'columns'    => [
                    'user' => 'Usuário',
                    'duration' => 'Duração',
                    'start-date' => 'Data de início',
                    'end-date' => 'Data de término',
                    'productivity' => 'Produtividade',
                ],
                'footer' => [
                    'real-duration' => 'Duração real',
                ],
            ],
            'components' => [
                'title' => 'Componentes',
                'add-action' => 'Adicionar uma linha',
                'columns'    => [
                    'product' => 'Produto',
                    'from' => 'De',
                    'to-consume' => 'A consumir',
                    'quantity' => 'Quantidade',
                    'uom' => 'Unidade de medida',
                ],
            ],
            'work-instruction' => [
                'title' => 'Instrução de trabalho',
                'entries' => [
                    'operation' => 'Operação',
                    'worksheet' => 'Folha de trabalho',
                ],
            ],
            'blocked-by' => [
                'title' => 'Bloqueado por',
                'fields' => [
                    'work-orders' => 'Ordens de trabalho',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'operation' => 'Operação',
            'work-center' => 'Centro de trabalho',
            'manufacturing-order' => 'Ordem de produção',
            'product' => 'Produto',
            'quantity-remaining' => 'Quantidade restante',
            'lot-serial' => 'Lote/Série',
            'start' => 'Início',
            'end' => 'Fim',
            'expected-duration' => 'Duração esperada',
            'real-duration' => 'Duração real',
            'status' => 'Status',
        ],
        'groups' => [
            'status' => 'Status',
            'work-center' => 'Centro de trabalho',
            'manufacturing-order' => 'Ordem de produção',
            'product' => 'Produto',
            'start' => 'Início',
            'end' => 'Fim',
        ],
        'filters' => [
            'work-order' => 'Ordem de trabalho',
            'status' => 'Status',
            'operation' => 'Operação',
            'work-center' => 'Centro de trabalho',
            'manufacturing-order' => 'Ordem de produção',
            'product' => 'Produto',
            'start' => 'Início',
            'end' => 'Fim',
            'created-at' => 'Criado em',
            'updated-at' => 'Atualizado em',
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Geral',
                'entries' => [
                    'work-order' => 'Ordem de trabalho',
                    'work-center' => 'Centro de trabalho',
                    'product' => 'Produto',
                    'quantity' => 'Quantidade',
                    'manufacturing-order' => 'Ordem de produção',
                    'lot-serial' => 'Número de lote/série',
                    'start-date' => 'Data de início',
                    'end-date' => 'Data de término',
                    'expected-duration' => 'Duração esperada',
                    'real-duration' => 'Duração real',
                ],
            ],
        ],
        'tabs' => [
            'time-tracking' => [
                'title' => 'Controle de tempo',
                'footer' => [
                    'real-duration' => 'Duração real',
                ],
            ],
            'components' => [
                'title' => 'Componentes',
            ],
            'work-instruction' => [
                'title' => 'Instrução de trabalho',
                'entries' => [
                    'operation' => 'Operação',
                    'worksheet' => 'Folha de trabalho',
                ],
            ],
            'blocked-by' => [
                'title' => 'Bloqueado por',
                'columns' => [
                    'work-order' => 'Ordem de trabalho',
                    'work-center' => 'Centro de trabalho',
                    'status' => 'Status',
                ],
            ],
        ],
    ],

    'pages' => [
        'list' => [
            'header-actions' => [
                'create' => [
                    'label' => 'Nova ordem de trabalho',
                ],
            ],
        ],
    ],
];
