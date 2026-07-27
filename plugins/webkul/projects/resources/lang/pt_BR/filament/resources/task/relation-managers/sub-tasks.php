<?php

return [
    'table' => [
        'header-actions' => [
            'create' => [
                'label' => 'Adicionar subtarefa',

                'notification' => [
                    'title' => 'Tarefa criada',
                    'body' => 'A tarefa foi criada com sucesso.',
                ],
            ],
        ],

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
];
