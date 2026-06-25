<?php

return [
    'navigation' => [
        'group' => 'Manutenção',
        'title' => 'Solicitações de manutenção',
    ],

    'form' => [
        'sections' => [
            'request' => [
                'title'  => 'Solicitação',
                'fields' => [
                    'name'                      => 'Solicitação',
                    'name-placeholder'          => 'ex.: tela não funciona',
                    'equipment'                 => 'Equipamento',
                    'category'                  => 'Categoria',
                    'requested-at'              => 'Data da solicitação',
                    'requested-at-hint-tooltip' => 'A data em que a solicitação de manutenção foi informada.',
                    'maintenance-type'          => 'Tipo de manutenção',
                    'recurrent'                 => 'Recorrente',
                    'repeat-every'              => 'Repetir a cada',
                    'maintenance-type-options'  => [
                        'corrective' => 'Corretiva',
                        'preventive' => 'Preventiva',
                    ],
                ],
                'tabs' => [
                    'notes' => [
                        'title'  => 'Notas',
                        'fields' => [
                            'description'             => 'Notas internas',
                            'description-placeholder' => 'Notas internas',
                        ],
                    ],
                    'instructions' => [
                        'title'  => 'Instruções',
                        'fields' => [
                            'instruction-type'         => 'Tipo de instrução',
                            'instruction-type-options' => [
                                'pdf'          => 'PDF',
                                'google-slide' => 'Google Slide',
                                'text'         => 'Texto',
                            ],
                            'instruction-pdf'              => 'PDF',
                            'instruction-google-slide'     => 'Google Slide',
                            'instruction-text'             => 'Descrição',
                            'instruction-text-placeholder' => 'Descrição',
                        ],
                    ],
                ],
            ],

            'settings' => [
                'title'  => 'Configurações',
                'fields' => [
                    'team'                      => 'Equipe',
                    'responsible'               => 'Responsável',
                    'scheduled-at'              => 'Data agendada',
                    'scheduled-at-hint-tooltip' => 'A data e hora em que este trabalho de manutenção está planejado para começar.',
                    'duration'                  => 'Duração',
                    'duration-hint-tooltip'     => 'Duração esperada da manutenção.',
                    'duration-suffix'           => 'horas',
                    'priority'                  => 'Prioridade',
                    'company'                   => 'Empresa',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'Assuntos',
            'creator'    => 'Criado pelo usuário',
            'technician' => 'Técnico',
            'category'   => 'Categoria',
            'stage'      => 'Etapa',
            'company'    => 'Empresa',
        ],

        'groups' => [
            'stage'       => 'Etapa',
            'assigned-to' => 'Atribuído a',
            'category'    => 'Categoria',
            'created-by'  => 'Criado por',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Solicitação de manutenção restaurada',
                    'body'  => 'A solicitação de manutenção foi restaurada com sucesso.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Solicitação de manutenção arquivada',
                    'body'  => 'A solicitação de manutenção foi arquivada com sucesso.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Solicitação de manutenção excluída',
                        'body'  => 'A solicitação de manutenção foi excluída permanentemente.',
                    ],
                    'error' => [
                        'title' => 'Solicitação de manutenção não pôde ser excluída',
                        'body'  => 'Esta solicitação de manutenção é referenciada por outro registro.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Solicitações de manutenção restauradas',
                    'body'  => 'As solicitações de manutenção selecionadas foram restauradas com sucesso.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Solicitações de manutenção arquivadas',
                    'body'  => 'As solicitações de manutenção selecionadas foram arquivadas com sucesso.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'request' => [
                'title'   => 'Solicitação',
                'entries' => [
                    'name'                     => 'Solicitação',
                    'equipment'                => 'Equipamento',
                    'category'                 => 'Categoria',
                    'requested-at'             => 'Data da solicitação',
                    'maintenance-type'         => 'Tipo de manutenção',
                    'instruction-type'         => 'Tipo de instrução',
                    'instruction-pdf'          => 'PDF',
                    'instruction-google-slide' => 'Google Slide',
                    'description'              => 'Notas internas',
                    'instruction-text'         => 'Descrição',
                ],
            ],

            'settings' => [
                'title'   => 'Configurações',
                'entries' => [
                    'team'            => 'Equipe',
                    'responsible'     => 'Responsável',
                    'scheduled-at'    => 'Data agendada',
                    'duration'        => 'Duração',
                    'duration-suffix' => 'horas',
                    'priority'        => 'Prioridade',
                    'company'         => 'Empresa',
                ],
            ],
        ],
    ],
];
