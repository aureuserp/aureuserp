<?php

return [
    'model-label'        => 'Lead',
    'plural-model-label' => 'Leads',

    'navigation' => [
        'title' => 'Leads',
    ],

    'views' => [
        'kanban' => 'Kanban',
        'list'   => 'Lista',
    ],

    'global-search' => [
        'contact' => 'Contato',
        'company' => 'Empresa',
    ],

    'form' => [
        'tabs' => [
            'notes'      => 'Notas',
            'extra-info' => 'Informações extras',
        ],

        'fieldsets' => [
            'marketing' => 'Marketing',
            'analysis'  => 'Análise',
        ],

        'fields' => [
            'name-placeholder'    => 'ex.: Preços de produtos',
            'probability'         => 'Probabilidade',
            'company-name'        => 'Nome da empresa',
            'address'             => 'Endereço',
            'street1'             => 'Rua...',
            'street2'             => 'Rua 2...',
            'city'                => 'Cidade',
            'zip'                 => 'CEP',
            'state'               => 'Estado',
            'country'             => 'País',
            'website'             => 'Site',
            'website-placeholder' => 'ex.: https://www.example.com',
            'salesperson'         => 'Vendedor',
            'team'                => 'Equipe de vendas',
            'contact-name'        => 'Nome do contato',
            'email'               => 'E-mail',
            'job-position'        => 'Cargo',
            'phone'               => 'Telefone',
            'priority'            => 'Prioridade',
            'tags'                => 'Tags',
            'company'             => 'Empresa',
            'campaign'            => 'Campanha',
            'medium'              => 'Meio',
            'source'              => 'Origem',
            'referred-by'         => 'Indicado por',
            'assignment-date'     => 'Data de atribuição',
            'closed-date'         => 'Data de fechamento',
        ],
    ],

    'infolist' => [
        'sections' => [
            'lost' => 'Perdido',
        ],

        'entries' => [
            'lost-reason'  => 'Motivo de perda',
            'closing-note' => 'Nota de fechamento',
        ],
    ],

    'table' => [
        'columns' => [
            'name'         => 'Lead',
            'contact-name' => 'Nome do contato',
            'company-name' => 'Nome da empresa',
            'email'        => 'E-mail',
            'phone'        => 'Telefone',
            'city'         => 'Cidade',
            'country'      => 'País',
            'salesperson'  => 'Vendedor',
            'team'         => 'Equipe de vendas',
            'priority'     => 'Prioridade',
            'tags'         => 'Tags',
            'source'       => 'Origem',
            'lost-reason'  => 'Motivo de perda',
            'created-at'   => 'Criado em',
        ],
    ],

    'actions' => [
        'create' => [
            'label' => 'Novo lead',
        ],

        'open' => [
            'label' => 'Abrir',
        ],

        'schedule-activity' => [
            'label' => 'Agendar atividade',
        ],

        'convert' => [
            'label' => 'Converter em oportunidade',

            'modal' => [
                'heading' => 'Converter em oportunidade',
                'submit'  => 'Criar oportunidade',
            ],

            'form' => [
                'customer'          => 'Cliente',
                'existing-customer' => 'Cliente existente',
                'salesperson'       => 'Vendedor',
                'team'              => 'Equipe de vendas',

                'customer-options' => [
                    'create' => 'Criar um novo cliente',
                    'link'   => 'Vincular a um cliente existente',
                    'none'   => 'Não vincular a um cliente',
                ],
            ],

            'notification' => [
                'title' => 'Convertido em oportunidade',
                'body'  => '":name" agora é uma oportunidade no funil.',

                'invalid' => [
                    'title' => 'Seleção inválida',
                    'body'  => 'O cliente, vendedor ou equipe de vendas selecionado não pertence à empresa deste lead.',
                ],
            ],
        ],

        'lost' => [
            'notification' => [
                'title' => 'Lead perdido',
                'body'  => 'O lead foi marcado como perdido e movido para a visualização Perdidos.',
            ],
        ],

        'restore' => [
            'notification' => [
                'title' => 'Lead restaurado',
                'body'  => 'O lead foi restaurado.',
            ],
        ],

        'force-delete' => [
            'notification' => [
                'title' => 'Lead excluído',
                'body'  => 'O lead foi excluído permanentemente.',
            ],
        ],
    ],
];
