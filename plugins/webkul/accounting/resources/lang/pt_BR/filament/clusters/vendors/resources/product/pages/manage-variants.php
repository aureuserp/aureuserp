<?php

return [
    'title' => 'Variantes',

    'form' => [
        'date'                   => 'Data',
        'employee'               => 'Colaborador',
        'description'            => 'Descrição',
        'time-spent'             => 'Tempo gasto',
        'time-spent-helper-text' => 'Tempo gasto em horas (ex.: 1,5 hora significa 1 hora e 30 minutos)',
    ],

    'table' => [
        'columns' => [
            'date'                   => 'Data',
            'employee'               => 'Colaborador',
            'description'            => 'Descrição',
            'time-spent'             => 'Tempo gasto',
            'time-spent-on-subtasks' => 'Tempo gasto em subtarefas',
            'total-time-spent'       => 'Tempo total gasto',
            'remaining-time'         => 'Tempo restante',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Variante excluída',
                    'body'  => 'A variante foi excluída com sucesso.',
                ],
            ],
        ],
    ],
];
