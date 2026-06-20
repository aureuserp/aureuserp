<?php

return [
    'navigation' => [
        'title' => 'Estágios da tarefa',
    ],

    'form' => [
        'name' => 'Nome',
        'project' => 'Projeto',
    ],

    'table' => [
        'columns' => [
            'name' => 'Nome',
            'project' => 'Projeto',
            'created-at' => 'Criado em',
            'updated-at' => 'Atualizado em',
        ],

        'groups' => [
            'project' => 'Projeto',
            'created-at' => 'Criado em',
        ],

        'filters' => [
            'project' => 'Projeto',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Estágio da tarefa atualizado',
                    'body' => 'O estágio da tarefa foi atualizado com sucesso.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Estágio da tarefa restaurado',
                    'body' => 'O estágio da tarefa foi restaurado com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Estágio da tarefa excluído',
                    'body' => 'O estágio da tarefa foi excluído com sucesso.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Estágio da tarefa excluído permanentemente',
                        'body' => 'O estágio da tarefa foi excluído permanentemente com sucesso.',
                    ],
                    'error' => [
                        'title' => 'Estágio da tarefa não pôde ser excluído',
                        'body' => 'O estágio da tarefa não pode ser excluído porque está em uso no momento.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Estágios da tarefa restaurados',
                    'body' => 'Os estágios da tarefa foram restaurados com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Estágios da tarefa excluídos',
                    'body' => 'Os estágios da tarefa foram excluídos com sucesso.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Estágios da tarefa excluídos permanentemente',
                    'body' => 'Os estágios da tarefa foram excluídos permanentemente com sucesso.',
                ],
            ],
        ],
    ],
];
