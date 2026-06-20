<?php

return [
    'form' => [
        'date' => 'Data',
        'employee' => 'Funcionário',
        'description' => 'Descrição',
        'time-spent' => 'Tempo gasto',
        'time-spent-helper-text' => 'Tempo gasto em horas (ex.: 1,5 hora significa 1 hora e 30 minutos)',
    ],

    'table' => [
        'header-actions' => [
            'create' => [
                'label' => 'Adicionar apontamento de horas',

                'notification' => [
                    'title' => 'Apontamento de horas criado',
                    'body' => 'O apontamento de horas foi criado com sucesso.',
                ],
            ],
        ],

        'columns' => [
            'date' => 'Data',
            'employee' => 'Funcionário',
            'description' => 'Descrição',
            'time-spent' => 'Tempo gasto',
            'time-spent-on-subtasks' => 'Tempo gasto em subtarefas',
            'total-time-spent' => 'Tempo total gasto',
            'remaining-time' => 'Tempo restante',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Apontamento de horas atualizado',
                    'body' => 'O apontamento de horas foi atualizado com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Apontamento de horas excluído',
                    'body' => 'O apontamento de horas foi excluído com sucesso.',
                ],
            ],
        ],
    ],
];
