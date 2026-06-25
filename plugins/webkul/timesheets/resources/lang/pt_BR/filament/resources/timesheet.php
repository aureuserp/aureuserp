<?php

return [
    'title' => 'Apontamentos de horas',

    'navigation' => [
        'title' => 'Apontamentos de horas',
        'group' => 'Projeto',
    ],

    'global-search' => [
        'project' => 'Projeto',
        'task'    => 'Tarefa',
        'date'    => 'Data',
    ],

    'form' => [
        'date'                   => 'Data',
        'employee'               => 'Colaborador',
        'project'                => 'Projeto',
        'task'                   => 'Tarefa',
        'description'            => 'Descrição',
        'time-spent'             => 'Tempo gasto',
        'time-spent-helper-text' => 'Tempo gasto em horas (ex.: 1,5 hora significa 1 hora e 30 minutos)',
    ],

    'table' => [
        'columns' => [
            'date'        => 'Data',
            'employee'    => 'Colaborador',
            'project'     => 'Projeto',
            'task'        => 'Tarefa',
            'description' => 'Descrição',
            'time-spent'  => 'Tempo gasto',
            'created-at'  => 'Criado em',
            'updated-at'  => 'Atualizado em',
        ],

        'groups' => [
            'date'     => 'Data',
            'employee' => 'Colaborador',
            'project'  => 'Projeto',
            'task'     => 'Tarefa',
            'creator'  => 'Criador',
        ],

        'filters' => [
            'date-from'  => 'Data inicial',
            'date-until' => 'Data final',
            'employee'   => 'Colaborador',
            'project'    => 'Projeto',
            'task'       => 'Tarefa',
            'creator'    => 'Criador',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Apontamento de horas atualizado',
                    'body'  => 'O apontamento de horas foi atualizado com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Apontamento de horas excluído',
                    'body'  => 'O apontamento de horas foi excluído com sucesso.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Apontamentos de horas excluídos',
                    'body'  => 'Os apontamentos de horas foram excluídos com sucesso.',
                ],
            ],
        ],
    ],
];
