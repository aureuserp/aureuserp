<?php

return [
    'navigation' => [
        'title' => 'Planos de atividade',
        'group' => 'Atividades',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Informações gerais',
                'fields' => [
                    'name' => 'Nome',
                    'status' => 'Status',
                    'department' => 'Departamento',
                    'company' => 'Empresa',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name' => 'Nome',
            'status' => 'Status',
            'department' => 'Departamento',
            'company' => 'Empresa',
            'manager' => 'Gerente',
            'created-by' => 'Criado por',
            'created-at' => 'Criado em',
            'updated-at' => 'Atualizado em',
        ],

        'filters' => [
            'name' => 'Nome',
            'plugin' => 'Plugin',
            'activity-types' => 'Tipos de atividade',
            'company' => 'Empresa',
            'department' => 'Departamento',
            'is-active' => 'Status',
            'updated-at' => 'Atualizado em',
            'created-at' => 'Criado em',
        ],

        'groups' => [
            'status' => 'Status',
            'name' => 'Nome',
            'created-by' => 'Criado por',
            'created-at' => 'Criado em',
            'updated-at' => 'Atualizado em',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Plano de atividade restaurado',
                    'body' => 'O plano de atividade foi restaurado com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Plano de atividade excluído',
                    'body' => 'O plano de atividade foi excluído com sucesso.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Plano de atividade excluído permanentemente',
                    'body' => 'O plano de atividade foi excluído permanentemente com sucesso.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Planos de atividade restaurados',
                    'body' => 'Os planos de atividade foram restaurados com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Planos de atividade excluídos',
                    'body' => 'Os planos de atividade foram excluídos com sucesso.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Planos de atividade excluídos permanentemente',
                    'body' => 'Os planos de atividade foram excluídos permanentemente com sucesso.',
                ],
            ],
        ],

        'empty-state' => [
            'create' => [
                'notification' => [
                    'title' => 'Plano de atividade criado',
                    'body' => 'O plano de atividade foi criado com sucesso.',
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
                    'status' => 'Status',
                    'department' => 'Departamento',
                    'manager' => 'Gerente',
                    'company' => 'Empresa',
                ],
            ],
        ],
    ],
];
