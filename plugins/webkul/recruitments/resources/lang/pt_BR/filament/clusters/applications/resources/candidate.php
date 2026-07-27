<?php

return [
    'title' => 'Candidato',

    'navigation' => [
        'title' => 'Candidatos',
    ],

    'global-search' => [
        'email-from' => 'E-mail de',
        'phone' => 'Telefone',
        'company' => 'Empresa',
        'degree' => 'Grau',
    ],

    'form' => [
        'sections' => [
            'basic-information' => [
                'title' => 'Informações básicas',

                'fields' => [
                    'full-name' => 'Nome completo',
                    'email' => 'Endereço de e-mail',
                    'phone' => 'Número de telefone',
                    'linkedin' => 'Perfil do LinkedIn',
                    'contact' => 'Contato',
                ],
            ],

            'additional-details' => [
                'title' => 'Detalhes adicionais',

                'fields' => [
                    'company' => 'Empresa',
                    'degree' => 'Grau',
                    'tags' => 'Tags',
                    'manager' => 'Gerente',
                    'availability-date' => 'Data de disponibilidade',

                    'priority-options' => [
                        'low' => 'Baixo',
                        'medium' => 'Médio',
                        'high' => 'Alto',
                    ],
                ],
            ],

            'status-and-evaluation' => [
                'title' => 'Status',

                'fields' => [
                    'active' => 'Ativa',
                    'evaluation' => 'Avaliação',
                ],
            ],

            'communication' => [
                'title' => 'Comunicação',

                'fields' => [
                    'cc-email' => 'E-mail CC',
                    'email-bounced' => 'E-mail devolvido',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name' => 'Nome completo',
            'tags' => 'Tags',
            'evaluation' => 'Avaliação',
        ],

        'filters' => [
            'company' => 'Empresa',
            'partner-name' => 'Contato',
            'degree' => 'Grau',
            'manager-name' => 'Gerente',
        ],

        'groups' => [
            'manager-name' => 'Gerente',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Candidato excluído',
                    'body' => 'Os candidatos foram excluídos com sucesso.',
                ],
            ],

            'empty-state-actions' => [
                'create' => [
                    'notification' => [
                        'title' => 'Candidato criado',
                        'body' => 'Os candidatos foram criados com sucesso.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Candidatos excluídos',
                    'body' => 'Os candidatos foram excluídos com sucesso.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'basic-information' => [
                'title' => 'Informações básicas',

                'entries' => [
                    'full-name' => 'Nome completo',
                    'email' => 'Endereço de e-mail',
                    'phone' => 'Número de telefone',
                    'linkedin' => 'Perfil do LinkedIn',
                    'contact' => 'Contato',
                ],
            ],

            'additional-details' => [
                'title' => 'Detalhes adicionais',

                'entries' => [
                    'company' => 'Empresa',
                    'degree' => 'Grau',
                    'tags' => 'Tags',
                    'manager' => 'Gerente',
                    'availability-date' => 'Data de disponibilidade',

                    'priority-options' => [
                        'low' => 'Baixo',
                        'medium' => 'Médio',
                        'high' => 'Alto',
                    ],
                ],
            ],

            'status-and-evaluation' => [
                'title' => 'Status',

                'entries' => [
                    'active' => 'Ativa',
                    'evaluation' => 'Avaliação',
                ],
            ],

            'communication' => [
                'title' => 'Comunicação',

                'entries' => [
                    'cc-email' => 'E-mail CC',
                    'email-bounced' => 'E-mail devolvido',
                ],
            ],
        ],
    ],
];
