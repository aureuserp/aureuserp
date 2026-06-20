<?php

return [
    'navigation' => [
        'title' => 'Estágios do projeto',
    ],

    'form' => [
        'name' => 'Nome',
    ],

    'table' => [
        'columns' => [
            'name' => 'Nome',
            'created-at' => 'Criado em',
            'updated-at' => 'Atualizado em',
        ],

        'groups' => [
            'name' => 'Nome',
            'is-completed' => 'Está concluído',
            'project' => 'Projeto',
            'created-at' => 'Criado em',
        ],

        'filters' => [
            'is-completed' => 'Está concluído',
            'project' => 'Projeto',
            'creator' => 'Criador',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Estágio do projeto atualizado',
                    'body' => 'O estágio do projeto foi atualizado com sucesso.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Estágio do projeto restaurado',
                    'body' => 'O estágio do projeto foi restaurado com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Estágio do projeto excluído',
                    'body' => 'O estágio do projeto foi excluído com sucesso.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Estágio do projeto excluído permanentemente',
                        'body' => 'O estágio do projeto foi excluído permanentemente com sucesso.',
                    ],
                    'error' => [
                        'title' => 'Estágio do projeto não pôde ser excluído',
                        'body' => 'O estágio do projeto não pode ser excluído porque está em uso no momento.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Estágios do projeto restaurados',
                    'body' => 'Os estágios do projeto foram restaurados com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Estágios do projeto excluídos',
                    'body' => 'Os estágios do projeto foram excluídos com sucesso.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Estágios do projeto excluídos permanentemente',
                    'body' => 'Os estágios do projeto foram excluídos permanentemente com sucesso.',
                ],
            ],
        ],
    ],
];
