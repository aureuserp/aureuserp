<?php

return [
    'title' => 'Departamentos',

    'navigation' => [
        'title' => 'Departamentos',
        'group' => 'Colaboradores',
    ],

    'form' => [
        'sections' => [
            'activity-type-details' => [
                'title' => 'Informações gerais',

                'fields' => [
                    'name' => 'Tipo de atividade',
                    'name-tooltip' => 'Informe o nome oficial do tipo de atividade',
                    'action' => 'Ação',
                    'default-user' => 'Usuário padrão',
                    'summary' => 'Resumo',
                    'note' => 'Nota',
                ],
            ],

            'delay-information' => [
                'title' => 'Informações de atraso',

                'fields' => [
                    'delay-count' => 'Contagem de atraso',
                    'delay-unit' => 'Unidade de atraso',
                    'delay-form' => 'Formulário de atraso',
                    'delay-form-helper-text' => 'Origem do cálculo do atraso',
                ],
            ],

            'advanced-information' => [
                'title' => 'Informações avançadas',

                'fields' => [
                    'icon' => 'Ícone',
                    'decoration-type' => 'Tipo de decoração',
                    'chaining-type' => 'Tipo de encadeamento',
                    'suggest' => 'Sugerir',
                    'trigger' => 'Disparar',
                ],
            ],

            'status-and-configuration-information' => [
                'title' => 'Status e configuração',

                'fields' => [
                    'status' => 'Status',
                    'keep-done-activities' => 'Manter atividades concluídas',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name' => 'Tipo de atividade',
            'summary' => 'Resumo',
            'planned-in' => 'Planejado em',
            'type' => 'Tipo',
            'action' => 'Ação',
            'status' => 'Status',
            'created-at' => 'Criado em',
            'updated-at' => 'Atualizado em',
        ],

        'groups' => [
            'name' => 'Nome',
            'action-category' => 'Categoria da ação',
            'status' => 'Status',
            'delay-count' => 'Contagem de atraso',
            'delay-unit' => 'Unidade de atraso',
            'delay-source' => 'Fonte do atraso',
            'associated-model' => 'Modelo associado',
            'chaining-type' => 'Tipo de encadeamento',
            'decoration-type' => 'Tipo de decoração',
            'default-user' => 'Usuário padrão',
            'creation-date' => 'Data de criação',
            'last-update' => 'Última atualização',
        ],

        'filters' => [
            'action' => 'Ação',
            'status' => 'Status',
            'has-delay' => 'Tem atraso',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Tipo de atividade restaurado',
                    'body' => 'O tipo de atividade foi restaurado com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Tipo de atividade excluído',
                    'body' => 'O tipo de atividade foi excluído com sucesso.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Tipo de atividade excluído permanentemente',
                        'body' => 'O tipo de atividade foi excluído permanentemente com sucesso.',
                    ],
                    'error' => [
                        'title' => 'Tipo de atividade não pôde ser excluído',
                        'body' => 'O tipo de atividade não pode ser excluído porque está em uso no momento.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Tipos de atividade restaurados',
                    'body' => 'Os tipos de atividade foram restaurados com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Tipos de atividade excluídos',
                    'body' => 'Os tipos de atividade foram excluídos com sucesso.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Tipos de atividade excluídos permanentemente',
                    'body' => 'Os tipos de atividade foram excluídos permanentemente com sucesso.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'activity-type-details' => [
                'title' => 'Informações gerais',

                'entries' => [
                    'name' => 'Tipo de atividade',
                    'name-tooltip' => 'Informe o nome oficial do tipo de atividade',
                    'action' => 'Ação',
                    'default-user' => 'Usuário padrão',
                    'plugin' => 'Plugin',
                    'summary' => 'Resumo',
                    'note' => 'Nota',
                ],
            ],

            'delay-information' => [
                'title' => 'Informações de atraso',

                'entries' => [
                    'delay-count' => 'Contagem de atraso',
                    'delay-unit' => 'Unidade de atraso',
                    'delay-form' => 'Formulário de atraso',
                    'delay-form-helper-text' => 'Origem do cálculo do atraso',
                ],
            ],

            'advanced-information' => [
                'title' => 'Informações avançadas',

                'entries' => [
                    'icon' => 'Ícone',
                    'decoration-type' => 'Tipo de decoração',
                    'chaining-type' => 'Tipo de encadeamento',
                    'suggest' => 'Sugerir',
                    'trigger' => 'Disparar',
                ],
            ],

            'status-and-configuration-information' => [
                'title' => 'Status e configuração',

                'entries' => [
                    'status' => 'Status',
                    'keep-done-activities' => 'Manter atividades concluídas',
                ],
            ],
        ],
    ],
];
