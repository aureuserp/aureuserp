<?php

return [
    'navigation' => [
        'title' => 'Marcos',
    ],

    'form' => [
        'name' => 'Nome',
        'deadline' => 'Prazo final',
        'is-completed' => 'Está concluído',
        'project' => 'Projeto',
    ],

    'table' => [
        'columns' => [
            'name' => 'Nome',
            'deadline' => 'Prazo final',
            'is-completed' => 'Está concluído',
            'completed-at' => 'Concluído em',
            'project' => 'Projeto',
            'creator' => 'Criador',
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
                    'title' => 'Marco atualizado',
                    'body' => 'O marco foi atualizado com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Marco excluído',
                    'body' => 'O marco foi excluído com sucesso.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Marcos excluídos',
                    'body' => 'Os marcos foram excluídos com sucesso.',
                ],
            ],
        ],
    ],
];
