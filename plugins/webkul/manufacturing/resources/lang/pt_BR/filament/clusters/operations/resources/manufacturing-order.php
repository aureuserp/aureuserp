<?php

return [
    'navigation' => [
        'title' => 'Ordens de produção',
        'group' => 'Operações',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Geral',
                'fields' => [
                    'product' => 'Produto',
                    'quantity' => 'Quantidade',
                    'uom' => 'Unidade de medida',
                    'bill-of-material' => 'Lista de materiais',
                    'scheduled-date' => 'Data agendada',
                    'scheduled-end' => 'Término agendado',
                    'responsible' => 'Responsável',
                    'to-produce' => 'A produzir',
                    'to-produce-placeholder' => 'Pré-visualização da imagem',
                    'uom-placeholder' => 'Unidade de medida',
                ],
            ],
        ],
        'tabs' => [
            'components' => [
                'title' => 'Componentes',
                'add-action' => 'Adicionar uma linha',
                'process-note' => 'Os componentes serão gerados conforme o processo de produção for construído.',
                'columns'      => [
                    'component' => 'Produto',
                    'from' => 'De',
                    'to-consume' => 'A consumir',
                    'to-consume-tooltip' => 'Quantidade disponível insuficiente',
                    'quantity' => 'Quantidade',
                    'uom' => 'Unidade de medida',
                    'forecast' => 'Previsão',
                ],
            ],
            'work-orders' => [
                'title' => 'Ordens de trabalho',
                'add-action' => 'Adicionar uma linha',
                'process-note' => 'As ordens de trabalho serão geradas depois que o processo de produção for configurado.',
                'columns'      => [
                    'operation' => 'Operação',
                    'work-center' => 'Centro de trabalho',
                    'product' => 'Produto',
                    'quantity-remaining' => 'Quantidade restante',
                    'quantity-produced' => 'Quantidade produzida',
                    'start' => 'Início',
                    'end' => 'Fim',
                    'expected-duration' => 'Duração esperada',
                    'real-duration' => 'Duração real',
                    'status' => 'Status',
                    'lot-serial' => 'Lote/Série',
                ],
                'actions' => [
                    'open-work-order' => [
                        'tooltip' => 'Abrir ordem de trabalho',
                    ],

                    'done' => [
                        'label' => 'Concluído',
                    ],
                ],
            ],
            'by-products' => [
                'title' => 'Subprodutos',
                'process-note' => 'Os subprodutos serão gerados conforme o processo de produção for construído.',
                'columns'      => [
                    'product' => 'Produto',
                    'to' => 'Para',
                    'to-produce' => 'A produzir',
                    'uom' => 'Unidade de medida',
                ],
            ],
            'miscellaneous' => [
                'title' => 'Diversos',
                'fields' => [
                    'operation-type' => 'Tipo de operação',
                    'source' => 'Origem',
                    'finished-products-location' => 'Local dos produtos acabados',
                    'company' => 'Empresa',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'reference' => 'Referência',
            'start' => 'Início',
            'end' => 'Fim',
            'deadline' => 'Prazo final',
            'product' => 'Produto',
            'lot-serial-number' => 'Número de lote/série',
            'bill-of-material' => 'Lista de materiais',
            'source' => 'Origem',
            'responsible' => 'Responsável',
            'mo-readiness' => 'Prontidão da OP',
            'component-status' => 'Status dos componentes',
            'quantity' => 'Quantidade',
            'uom' => 'Unidade de medida',
            'consumption-efficiency' => 'Eficiência de consumo',
            'expected-duration' => 'Duração esperada',
            'real-duration' => 'Duração real',
            'company' => 'Empresa',
            'state' => 'Estado',
        ],
        'groups' => [
            'state' => 'Estado',
            'product' => 'Produto',
            'bill-of-material' => 'Lista de materiais',
            'responsible' => 'Responsável',
            'deadline' => 'Prazo final',
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Geral',
                'entries' => [
                    'product' => 'Produto',
                    'scheduled-date' => 'Data agendada',
                    'responsible' => 'Responsável',
                    'quantity' => 'Quantidade',
                    'uom' => 'Unidade de medida',
                    'bill-of-material' => 'Lista de materiais',
                    'operation-type' => 'Tipo de operação',
                    'consumption-efficiency' => 'Eficiência de consumo',
                ],
            ],
        ],
        'tabs' => [
            'components' => [
                'title' => 'Componentes',
                'process-note' => 'Os componentes estarão disponíveis depois que o processo de produção for configurado.',
                'columns'      => [
                    'component' => 'Componente',
                    'quantity' => 'Quantidade',
                    'uom' => 'Unidade de medida',
                ],
            ],
            'work-orders' => [
                'title' => 'Ordens de trabalho',
                'process-note' => 'As ordens de trabalho estarão disponíveis depois que o processo de produção for configurado.',
                'columns'      => [
                    'operation' => 'Operação',
                    'work-center' => 'Centro de trabalho',
                    'product' => 'Produto',
                    'quantity-remaining' => 'Quantidade restante',
                    'expected-duration' => 'Duração esperada',
                    'real-duration' => 'Duração real',
                    'lot-serial' => 'Lote/Série',
                    'start' => 'Início',
                    'end' => 'Fim',
                ],
            ],
            'by-products' => [
                'title' => 'Subprodutos',
                'process-note' => 'Os subprodutos estarão disponíveis depois que o processo de produção for configurado.',
                'columns'      => [
                    'product' => 'Produto',
                    'to' => 'Para',
                    'to-produce' => 'A produzir',
                    'uom' => 'Unidade de medida',
                ],
            ],
            'miscellaneous' => [
                'title' => 'Diversos',
                'entries' => [
                    'operation-type' => 'Tipo de operação',
                    'source' => 'Origem',
                    'finished-products-location' => 'Local dos produtos acabados',
                    'company' => 'Empresa',
                ],
            ],
        ],
    ],

    'pages' => [
        'shared' => [
            'header-actions' => [
                'confirm' => [
                    'label' => 'Confirmar',
                    'notification' => [
                        'title' => 'Ordem de produção confirmada',
                    ],
                ],

                'cancel' => [
                    'label' => 'Cancelar',
                    'notification' => [
                        'title' => 'Ordem de produção cancelada',
                    ],
                ],
            ],
        ],
    ],
];
