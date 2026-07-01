<?php

return [
    'navigation' => [
        'group' => 'Manutenção',
        'title' => 'Equipamento',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Informações gerais',
                'fields' => [
                    'name' => 'Nome',
                    'note' => 'Descrição',
                ],
            ],

            'settings' => [
                'title' => 'Configurações',
                'fields' => [
                    'category' => 'Categoria do equipamento',
                    'team' => 'Equipe de manutenção',
                    'company' => 'Empresa',
                    'technician' => 'Técnico',
                    'owner' => 'Proprietário',
                    'location' => 'Usado no local',
                ],
            ],

            'product-information' => [
                'title' => 'Informações do produto',
                'fields' => [
                    'partner' => 'Fornecedor',
                    'partner-ref' => 'Referência do fornecedor',
                    'model' => 'Modelo',
                    'serial-no' => 'Número de série',
                    'effective-date' => 'Data efetiva',
                    'effective-date-hint-tooltip' => 'Usada como ponto de partida para calcular o tempo médio entre falhas.',
                    'cost' => 'Custo',
                    'warranty-date' => 'Data de expiração da garantia',
                ],
            ],

            'maintenance' => [
                'title' => 'Manutenção',
                'fields' => [
                    'expected-mtbf' => 'Tempo médio esperado entre falhas',
                ],
                'suffixes' => [
                    'days' => 'dias',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name' => 'Nome do equipamento',
            'owner' => 'Proprietário',
            'serial-no' => 'Número de série',
            'category' => 'Categoria do equipamento',
            'technician' => 'Técnico',
            'company' => 'Empresa',
            'created-at' => 'Criado em',
        ],

        'filters' => [
            'category' => 'Categoria do equipamento',
            'team' => 'Equipe de manutenção',
            'technician' => 'Técnico',
        ],

        'groups' => [
            'category' => 'Categoria do equipamento',
            'owner' => 'Proprietário',
            'technician' => 'Técnico',
            'vendor' => 'Fornecedor',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Equipamento atualizado',
                    'body' => 'O equipamento foi atualizado com sucesso.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Equipamento restaurado',
                    'body' => 'O equipamento foi restaurado com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Equipamento arquivado',
                    'body' => 'O equipamento foi arquivado com sucesso.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Equipamento excluído',
                        'body' => 'O equipamento foi excluído permanentemente.',
                    ],

                    'error' => [
                        'title' => 'Equipamento não pôde ser excluído',
                        'body' => 'Este equipamento é referenciado por outro registro.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Equipamento restaurado',
                    'body' => 'Os equipamentos selecionados foram restaurados com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Equipamento arquivado',
                    'body' => 'Os equipamentos selecionados foram arquivados com sucesso.',
                ],
            ],
        ],

        'empty-state' => [
            'create' => [
                'notification' => [
                    'title' => 'Equipamento criado',
                    'body' => 'O equipamento foi criado com sucesso.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Informações gerais',
                'entries' => [
                    'name' => 'Nome',
                    'note' => 'Descrição',
                ],
            ],

            'settings' => [
                'title' => 'Configurações',
                'entries' => [
                    'category' => 'Categoria do equipamento',
                    'team' => 'Equipe de manutenção',
                    'company' => 'Empresa',
                    'technician' => 'Técnico',
                    'owner' => 'Proprietário',
                    'location' => 'Usado no local',
                ],
            ],

            'product-information' => [
                'title' => 'Informações do produto',
                'entries' => [
                    'partner' => 'Fornecedor',
                    'partner-ref' => 'Referência do fornecedor',
                    'model' => 'Modelo',
                    'serial-no' => 'Número de série',
                    'effective-date' => 'Data efetiva',
                    'cost' => 'Custo',
                    'warranty-date' => 'Data de expiração da garantia',
                ],
            ],

            'maintenance' => [
                'title' => 'Manutenção',
                'entries' => [
                    'expected-mtbf' => 'Tempo médio esperado entre falhas',
                    'maintenance-count' => 'Contagem de manutenções',
                    'maintenance-open-count' => 'Contagem de manutenções abertas',
                    'assigned-at' => 'Data de atribuição',
                    'scraped-at' => 'Data de descarte',
                ],
                'suffixes' => [
                    'days' => 'dias',
                ],
            ],
        ],
    ],
];
