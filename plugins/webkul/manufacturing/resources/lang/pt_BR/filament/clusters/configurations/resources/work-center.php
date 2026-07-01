<?php

return [
    'navigation' => [
        'title' => 'Centros de trabalho',
        'group' => 'Configuração',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Geral',
                'fields' => [
                    'name' => 'Nome',
                    'name-placeholder' => 'ex.: Linha de montagem 1',
                    'code' => 'Código',
                    'code-placeholder' => 'ex.: LM1',
                    'working-state' => 'Estado de trabalho',
                    'color' => 'Cor',
                    'tags' => 'Tag',
                    'alternative-work-centers' => 'Centros de trabalho alternativos',
                    'company' => 'Empresa',
                    'calendar' => 'Horário de trabalho',
                ],
            ],

            'information' => [
                'title' => 'Informações gerais',
                'fieldsets' => [
                    'production-information' => 'Informações de produção',
                    'costing-information' => 'Informações de custo',
                ],
                'fields' => [
                    'default-capacity' => 'Capacidade padrão',
                    'time-efficiency' => 'Eficiência de tempo',
                    'oee-target' => 'Meta de OEE',
                    'costs-per-hour' => 'Custo por hora',
                    'cost-suffix' => 'por hora',
                    'setup-time' => 'Tempo de preparação',
                    'cleanup-time' => 'Tempo de limpeza',
                    'time-suffix' => 'minutos',
                ],
            ],

            'description' => [
                'title' => 'Descrição',
                'fields' => [
                    'note' => 'Descrição',
                    'note-placeholder' => 'Descrição do centro de trabalho...',
                ],
            ],

            'specific-capacity' => [
                'title' => 'Capacidade específica',
                'fields' => [
                    'records' => 'Capacidade específica',
                ],
                'columns' => [
                    'product' => 'Produto',
                    'product-uom' => 'Unidade de medida',
                    'capacity' => 'Capacidade',
                    'setup-time' => 'Tempo de preparação',
                    'cleanup-time' => 'Tempo de limpeza',
                ],
                'actions' => [
                    'add' => 'Adicionar uma linha',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name' => 'Nome',
            'code' => 'Código',
            'company' => 'Empresa',
            'calendar' => 'Horário de trabalho',
            'working-state' => 'Estado de trabalho',
            'default-capacity' => 'Capacidade',
            'time-efficiency' => 'Eficiência',
            'costs-per-hour' => 'Custo por hora',
            'deleted-at' => 'Excluído em',
            'created-at' => 'Criado em',
            'updated-at' => 'Atualizado em',
        ],

        'groups' => [
            'company' => 'Empresa',
        ],

        'filters' => [
            'company' => 'Empresa',
            'working-state' => 'Estado de trabalho',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Centro de trabalho restaurado',
                    'body' => 'O centro de trabalho foi restaurado com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Centro de trabalho arquivado',
                    'body' => 'O centro de trabalho foi arquivado com sucesso.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Centro de trabalho excluído',
                        'body' => 'O centro de trabalho foi excluído permanentemente.',
                    ],

                    'error' => [
                        'title' => 'Não foi possível excluir o centro de trabalho',
                        'body' => 'O centro de trabalho não pode ser excluído porque está em uso no momento.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Centros de trabalho restaurados',
                    'body' => 'Os centros de trabalho selecionados foram restaurados com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Centros de trabalho arquivados',
                    'body' => 'Os centros de trabalho selecionados foram arquivados com sucesso.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Centros de trabalho excluídos',
                        'body' => 'Os centros de trabalho selecionados foram excluídos permanentemente.',
                    ],

                    'error' => [
                        'title' => 'Não foi possível excluir os centros de trabalho',
                        'body' => 'Um ou mais centros de trabalho selecionados estão em uso no momento.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Informações gerais',

                'entries' => [
                    'name' => 'Nome do centro de trabalho',
                    'code' => 'Código',
                    'working-state' => 'Estado de trabalho',
                    'tags' => 'Tag',
                    'alternative-work-centers' => 'Centros de trabalho alternativos',
                    'company' => 'Empresa',
                    'calendar' => 'Horário de trabalho',
                ],
            ],

            'information' => [
                'title' => 'Informações gerais',
                'fieldsets' => [
                    'production-information' => 'Informações de produção',
                    'costing-information' => 'Informações de custo',
                ],

                'entries' => [
                    'default-capacity' => 'Capacidade padrão',
                    'time-efficiency' => 'Eficiência de tempo',
                    'oee-target' => 'Meta de OEE',
                    'costs-per-hour' => 'Custo por hora',
                    'cost-suffix' => 'por centro de trabalho',
                    'setup-time' => 'Tempo de preparação',
                    'cleanup-time' => 'Tempo de limpeza',
                    'time-suffix' => 'minutos',
                ],
            ],

            'description' => [
                'title' => 'Descrição',
                'entries' => [
                    'note' => 'Descrição',
                ],
            ],

            'specific-capacity' => [
                'title' => 'Capacidades específicas',
                'columns' => [
                    'product' => 'Produto',
                    'product-uom' => 'Unidade de medida',
                    'capacity' => 'Capacidade',
                    'setup-time' => 'Tempo de preparação',
                    'cleanup-time' => 'Tempo de limpeza',
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
        ],
    ],
];
