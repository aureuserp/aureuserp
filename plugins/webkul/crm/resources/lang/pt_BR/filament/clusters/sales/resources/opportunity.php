<?php

return [
    'model-label'        => 'Oportunidade',
    'plural-model-label' => 'Oportunidades',

    'navigation' => [
        'title' => 'Meu funil',
    ],

    'global-search' => [
        'customer' => 'Cliente',
        'stage'    => 'Etapa',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Oportunidade',

                'fields' => [
                    'name'             => 'Oportunidade',
                    'name-placeholder' => 'ex.: Preços de produtos',
                    'expected-revenue' => 'Receita esperada',
                    'probability'      => 'Probabilidade',
                    'customer'         => 'Cliente',
                    'email'            => 'E-mail',
                    'phone'            => 'Telefone',
                ],
            ],

            'notes' => [
                'title' => 'Notas internas',
            ],

            'sales' => [
                'title' => 'Vendas',

                'fields' => [
                    'salesperson'      => 'Vendedor',
                    'team'             => 'Equipe de vendas',
                    'company'          => 'Empresa',
                    'expected-closing' => 'Fechamento previsto',
                    'priority'         => 'Prioridade',
                    'tags'             => 'Tags',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'lost' => [
                'title' => 'Perdida',

                'entries' => [
                    'lost-reason' => 'Motivo de perda',
                    'feedback'    => 'Nota de fechamento',
                ],
            ],

            'general' => [
                'title' => 'Oportunidade',

                'entries' => [
                    'expected-revenue' => 'Receita esperada',
                    'probability'      => 'Probabilidade',
                    'customer'         => 'Cliente',
                    'email'            => 'E-mail',
                    'phone'            => 'Telefone',
                ],
            ],

            'notes' => [
                'title' => 'Notas internas',
            ],

            'sales' => [
                'title' => 'Vendas',

                'entries' => [
                    'salesperson'      => 'Vendedor',
                    'team'             => 'Equipe de vendas',
                    'company'          => 'Empresa',
                    'expected-closing' => 'Fechamento previsto',
                    'priority'         => 'Prioridade',
                    'tags'             => 'Tags',
                    'closed-at'        => 'Fechado em',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'             => 'Oportunidade',
            'customer'         => 'Cliente',
            'stage'            => 'Etapa',
            'expected-revenue' => 'Receita esperada',
            'probability'      => 'Probabilidade',
            'priority'         => 'Prioridade',
            'salesperson'      => 'Vendedor',
            'team'             => 'Equipe de vendas',
            'expected-closing' => 'Fechamento previsto',
            'tags'             => 'Tags',
            'email'            => 'E-mail',
            'phone'            => 'Telefone',
            'medium'           => 'Meio',
            'lost-reason'      => 'Motivo de perda',
            'created-at'       => 'Criado em',
        ],

        'summaries' => [
            'total' => 'Total',
        ],

        'groups' => [
            'stage'            => 'Etapa',
            'team'             => 'Equipe de vendas',
            'salesperson'      => 'Vendedor',
            'priority'         => 'Prioridade',
            'expected-closing' => 'Fechamento previsto',
        ],

        'actions' => [
            'schedule-activity' => [
                'label' => 'Agendar atividade',
            ],

            'restore' => [
                'label' => 'Restaurar',

                'notification' => [
                    'title' => 'Oportunidade restaurada',
                    'body'  => 'A oportunidade foi restaurada no funil.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Oportunidade excluída',
                    'body'  => 'A oportunidade foi excluída permanentemente.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Oportunidades restauradas',
                    'body'  => 'As oportunidades foram restauradas no funil.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Oportunidades excluídas',
                    'body'  => 'As oportunidades foram excluídas permanentemente.',
                ],
            ],
        ],
    ],

    'actions' => [
        'new-quotation' => [
            'label'             => 'Nova cotação',
            'modal-heading'     => 'Nova cotação',
            'modal-description' => 'Esta oportunidade ainda não tem cliente. Escolha o cliente da cotação.',
            'submit'            => 'Continuar',
            'subheading'        => 'Da oportunidade: :name',
        ],

        'won' => [
            'label' => 'Ganha',

            'modal' => [
                'heading'     => 'Marcar como ganha',
                'description' => 'A oportunidade será movida para a etapa de ganho com probabilidade de 100%.',
            ],

            'notification' => [
                'success' => [
                    'title' => 'Oportunidade ganha',
                    'body'  => 'A oportunidade foi marcada como ganha.',
                ],

                'no-won-stage' => [
                    'title' => 'Nenhuma etapa de ganho',
                    'body'  => 'Primeiro, marque uma etapa como "É etapa de ganho?" em CRM → Configuração → Etapas.',
                ],
            ],
        ],

        'lost' => [
            'label' => 'Perdida',

            'modal' => [
                'heading' => 'Marcar como perdida',
                'submit'  => 'Marcar como perdida',
            ],

            'form' => [
                'lost-reason' => 'Motivo de perda',
                'feedback'    => 'Nota de fechamento',
            ],

            'notification' => [
                'title' => 'Oportunidade perdida',
                'body'  => 'A oportunidade foi marcada como perdida e movida para a visualização Perdidas.',
            ],
        ],
    ],

    'quotations' => [
        'title' => 'Cotações',

        'columns' => [
            'number'      => 'Número',
            'date'        => 'Data',
            'customer'    => 'Cliente',
            'salesperson' => 'Vendedor',
            'total'       => 'Total',
            'status'      => 'Status',
        ],

        'empty' => [
            'heading'     => 'Nenhuma cotação ainda',
            'description' => 'Use "Nova cotação" para criar uma cotação a partir desta oportunidade.',
        ],
    ],
];
