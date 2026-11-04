<?php

return [
    'title'        => 'Depósito de ativos',
    'plural-title' => 'Depósitos de ativos',

    'navigation' => [
        'title' => 'Depósitos',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Detalhes do depósito',
                'fields' => [
                    'name'             => 'Nome',
                    'code'             => 'Código',
                    'code-help'        => 'Código interno opcional, único na empresa.',
                    'note'             => 'Endereço / Localização e observações',
                    'note-placeholder' => 'Observações de localização ou armazenamento.',
                ],
            ],

            'settings' => [
                'title'  => 'Configurações',
                'fields' => [
                    'company'        => 'Empresa',
                    'custodian'      => 'Fiel depositário',
                    'custodian-help' => 'Funcionário responsável pelos ativos armazenados neste depósito.',
                    'is-active'      => 'Ativo',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'         => 'Nome',
            'code'         => 'Código',
            'custodian'    => 'Fiel depositário',
            'assets-count' => 'Ativos armazenados',
            'total-cost'   => 'Avaliação total',
            'is-active'    => 'Ativo',
            'company'      => 'Empresa',
            'creator'      => 'Criado por',
            'deleted-at'   => 'Arquivado em',
            'created-at'   => 'Data de criação',
            'updated-at'   => 'Data de atualização',
        ],

        'filters' => [
            'custodian' => 'Fiel depositário',
            'is-active' => 'Ativo',
            'company'   => 'Empresa',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Depósito atualizado',
                    'body'  => 'O depósito foi atualizado com sucesso.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'error' => [
                        'title' => 'Não foi possível excluir o depósito',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'   => 'Detalhes do depósito',
                'entries' => [
                    'name'      => 'Nome',
                    'code'      => 'Código',
                    'custodian' => 'Fiel depositário',
                    'company'   => 'Empresa',
                    'is-active' => 'Ativo',
                    'note'      => 'Endereço / Localização e observações',
                ],
            ],

            'audit' => [
                'title'   => 'Auditoria',
                'entries' => [
                    'creator'    => 'Criado por',
                    'created-at' => 'Data de criação',
                    'updated-at' => 'Data de atualização',
                ],
            ],
        ],
    ],

    'relation-manager' => [
        'warehouse-assets' => [
            'title'   => 'Ativos armazenados',
            'columns' => [
                'asset-tag'     => 'Etiqueta do ativo',
                'asset-name'    => 'Nome do ativo',
                'product'       => 'Produto',
                'model'         => 'Modelo',
                'serial-no'     => 'Nº de série',
                'category'      => 'Categoria',
                'condition'     => 'Condição',
                'cost'          => 'Custo',
                'purchase-date' => 'Data de compra',
            ],
            'actions' => [
                'issue' => 'Entregar ativo',
            ],
        ],
    ],

    'pages' => [
        'list' => [
            'header-actions' => [
                'create' => [
                    'label'        => 'Novo depósito de ativos',
                    'notification' => [
                        'title' => 'Depósito criado',
                        'body'  => 'O depósito foi criado com sucesso.',
                    ],
                ],
            ],
            'views' => [
                'all'      => 'Todos os depósitos',
                'archived' => 'Depósitos arquivados',
            ],
        ],
        'create' => [
            'notification' => [
                'title' => 'Depósito criado',
                'body'  => 'O depósito foi criado com sucesso.',
            ],
        ],
        'edit' => [
            'notification' => [
                'title' => 'Depósito atualizado',
                'body'  => 'O depósito foi atualizado com sucesso.',
            ],
        ],
    ],
];
