<?php

return [
    'navigation' => [
        'title' => 'Planos de atividade',
    ],

    'form' => [
        'name' => 'Nome',
        'status' => 'Status',
    ],

    'table' => [
        'columns' => [
            'name' => 'Nome',
            'status' => 'Status',
            'created-at' => 'Criado em',
            'updated-at' => 'Atualizado em',
        ],

        'groups' => [
            'name' => 'Nome',
            'status' => 'Status',
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
        'name' => 'Nome',
        'status' => 'Status',
    ],
];
