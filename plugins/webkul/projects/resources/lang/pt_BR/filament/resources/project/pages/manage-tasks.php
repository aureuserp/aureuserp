<?php

return [
    'title' => 'Tarefas',

    'header-actions' => [
        'create' => [
            'label' => 'Nova tarefa',
        ],
    ],

    'table' => [
        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Tarefa restaurada',
                    'body' => 'A tarefa foi restaurada com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Tarefa excluída',
                    'body' => 'A tarefa foi excluída com sucesso.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Tarefa excluída permanentemente',
                    'body' => 'A tarefa foi excluída permanentemente com sucesso.',
                ],
            ],
        ],
    ],

    'tabs' => [
        'open-tasks' => 'Tarefas abertas',
        'my-tasks' => 'Minhas tarefas',
        'unassigned-tasks' => 'Tarefas não atribuídas',
        'closed-tasks' => 'Tarefas fechadas',
        'starred-tasks' => 'Tarefas favoritas',
        'archived-tasks' => 'Tarefas arquivadas',
    ],
];
