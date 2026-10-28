<?php

return [
    'title'        => 'Local de Ativos e Custódias',
    'plural-title' => 'Locais de Ativos e Custódias',

    'navigation' => [
        'title' => 'Locais de Ativos e Custódias',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Detalhes do Local',
                'fields' => [
                    'name'              => 'Nome',
                    'parent'            => 'Local principal',
                    'usage-type'        => 'Tipo de uso',
                    'usage-type-help'   => 'Os locais de ativos fixos contêm ativos implantados pela empresa; as zonas de custódia classificam a custódia pessoal dos funcionários.',
                    'code'              => 'Código',
                    'code-help'         => 'Código interno opcional, exclusivo na empresa.',
                    'type'              => 'Tipo',
                    'type-name'         => 'Nome do tipo',
                    'type-icon'         => 'Ícone',
                    'type-color'        => 'Cor',
                    'note'              => 'Endereço / Localização e observações',
                    'note-placeholder'  => 'Edifício, andar, área de campo ou outros detalhes de localização.',
                ],
            ],

            'settings' => [
                'title'  => 'Configurações',
                'fields' => [
                    'company'                  => 'Empresa',
                    'default-custodian'        => 'Custodiante padrão do local',
                    'default-custodian-help'   => 'Funcionário atribuído automaticamente como receptor de custódia quando os ativos são despachados para este local.',
                    'is-active'                => 'Ativo',
                    'requires-custodian'       => 'Requer custodiante',
                    'requires-custodian-help'  => 'Os locais de campo e de veículos exigem um receptor designado para cada entrega de custódia.',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'               => 'Nome',
            'full-name'          => 'Nome completo',
            'usage-type'         => 'Tipo de uso',
            'code'               => 'Código',
            'type'               => 'Tipo',
            'default-custodian'  => 'Custodiante padrão',
            'is-active'          => 'Ativo',
            'requires-custodian' => 'Requer custodiante',
            'assets-count'       => 'Ativos',
            'total-cost'         => 'Avaliação total',
            'company'            => 'Empresa',
            'creator'            => 'Criado por',
            'deleted-at'         => 'Arquivado em',
            'created-at'         => 'Criado em',
            'updated-at'         => 'Atualizado em',
        ],

        'filters' => [
            'usage-type'         => 'Tipo de uso',
            'parent'             => 'Local principal',
            'type'               => 'Tipo',
            'is-active'          => 'Ativo',
            'requires-custodian' => 'Requer custodiante',
            'company'            => 'Empresa',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Local atualizado',
                    'body'  => 'O local foi atualizado com sucesso.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'error' => [
                        'title' => 'Não foi possível excluir o local',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'   => 'Detalhes do Local',
                'entries' => [
                    'name'               => 'Nome',
                    'full-name'          => 'Nome completo',
                    'parent'             => 'Local principal',
                    'usage-type'         => 'Tipo de uso',
                    'code'               => 'Código',
                    'type'               => 'Tipo',
                    'company'            => 'Empresa',
                    'is-active'          => 'Ativo',
                    'requires-custodian' => 'Requer custodiante',
                    'default-custodian'  => 'Custodiante padrão',
                    'note'               => 'Endereço / Localização e observações',
                ],
            ],

            'audit' => [
                'title'   => 'Auditoria',
                'entries' => [
                    'creator'    => 'Criado por',
                    'created-at' => 'Criado em',
                    'updated-at' => 'Atualizado em',
                ],
            ],
        ],
    ],

    'relation-manager' => [
        'site-assets' => [
            'title'   => 'Conteúdo do Local',
            'columns' => [
                'asset-tag'          => 'Etiqueta do ativo',
                'asset-name'         => 'Ativo',
                'category'           => 'Categoria',
                'current-site'       => 'Local / Sub-local',
                'serial-no'          => 'Nº de série',
                'receiver'           => 'Recebido por',
                'condition-out'      => 'Condição de saída',
                'condition-in'       => 'Condição de entrada',
                'cost'               => 'Custo',
                'issued-at'          => 'Emitido em',
                'expected-return-at' => 'Retorno previsto',
                'status'             => 'Status',
                'ended-type'         => 'Finalizado como',
                'issued-by'          => 'Emitido por',
            ],
            'filters' => [
                'status' => 'Status',
            ],
            'status' => [
                'open'   => 'No local',
                'closed' => 'Devolvido',
            ],
            'tabs' => [
                'all'           => 'Todos (incluindo sublocais)',
                'direct'        => 'Ativos diretos',
                'subsites'      => 'Sublocais',
                'with-children' => ':site (Todos)',
                'only'          => ':site (Apenas direto)',
            ],
        ],
    ],

    'pages' => [
        'list' => [
            'header-actions' => [
                'create' => [
                    'label'        => 'Novo Local de Ativo',
                    'notification' => [
                        'title' => 'Local criado',
                        'body'  => 'O local foi criado com sucesso.',
                    ],
                ],
            ],
            'views' => [
                'all'             => 'Todos os locais',
                'asset-locations' => 'Locais de ativos fixos',
                'custody-zones'   => 'Zonas de custódia',
                'archived'        => 'Locais arquivados',
            ],
        ],
        'create' => [
            'notification' => [
                'title' => 'Local criado',
                'body'  => 'O local foi criado com sucesso.',
            ],
        ],
        'edit' => [
            'notification' => [
                'title' => 'Local atualizado',
                'body'  => 'O local foi atualizado com sucesso.',
            ],
        ],
    ],
];
