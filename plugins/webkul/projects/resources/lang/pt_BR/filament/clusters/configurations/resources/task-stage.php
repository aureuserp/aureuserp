<?php

return [
    'navigation' => [
        'title' => 'Etapas da tarefa',
    ],

    'form' => [
        'name'    => 'Nome',
        'project' => 'Projeto',
    ],

    'table' => [
        'columns' => [
            'name'       => 'Nome',
            'project'    => 'Projeto',
            'created-at' => 'Criado em',
            'updated-at' => 'Atualizado em',
        ],

        'groups' => [
            'project'    => 'Projeto',
            'created-at' => 'Criado em',
        ],

        'filters' => [
            'project' => 'Projeto',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Etapa da tarefa atualizado',
                    'body'  => 'O etapa da tarefa foi atualizado com sucesso.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Etapa da tarefa restaurado',
                    'body'  => 'O etapa da tarefa foi restaurado com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Etapa da tarefa excluído',
                    'body'  => 'O etapa da tarefa foi excluído com sucesso.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Etapa da tarefa excluído permanentemente',
                        'body'  => 'O etapa da tarefa foi excluído permanentemente com sucesso.',
                    ],
                    'error' => [
                        'title' => 'Etapa da tarefa não pôde ser excluído',
                        'body'  => 'O etapa da tarefa não pode ser excluído porque está em uso no momento.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Etapas da tarefa restaurados',
                    'body'  => 'Os etapas da tarefa foram restaurados com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Etapas da tarefa excluídos',
                    'body'  => 'Os etapas da tarefa foram excluídos com sucesso.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Etapas da tarefa excluídos permanentemente',
                    'body'  => 'Os etapas da tarefa foram excluídos permanentemente com sucesso.',
                ],
            ],
        ],
    ],
];
