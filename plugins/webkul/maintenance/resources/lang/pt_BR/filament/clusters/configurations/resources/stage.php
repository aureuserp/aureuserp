<?php

return [
    'navigation' => [
        'title' => 'Estágios',
    ],

    'form' => [
        'fields' => [
            'name' => 'Nome',
            'done' => 'Concluído',
        ],
    ],

    'table' => [
        'columns' => [
            'name' => 'Nome',
            'done' => 'Concluído',
            'created-at' => 'Criado em',
        ],

        'groups' => [
            'done' => 'Concluído',
            'created-at' => 'Criado em',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Estágio atualizado',
                    'body' => 'O estágio foi atualizado com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Estágio excluído',
                    'body' => 'O estágio foi excluído com sucesso.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Estágios excluídos',
                    'body' => 'Os estágios foram excluídos com sucesso.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Informações gerais',

                'entries' => [
                    'name' => 'Nome',
                    'done' => 'Concluído',
                ],
            ],
        ],
    ],
];
