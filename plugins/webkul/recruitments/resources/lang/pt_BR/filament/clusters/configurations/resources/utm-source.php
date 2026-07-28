<?php

return [
    'title' => 'Origens',

    'navigation' => [
        'title' => 'Origens',
        'group' => 'UTMs',
    ],

    'groups' => [
        'status' => 'Status',
        'created-by' => 'Criado por',
        'created-at' => 'Criado em',
        'updated-at' => 'Atualizado em',
    ],

    'form' => [
        'fields' => [
            'name' => 'Nome',
            'name-placeholder' => 'Informe o nome da origem',
            'status' => 'Status',
        ],
    ],

    'table' => [
        'columns' => [
            'id' => 'ID',
            'name' => 'Nome',
            'status' => 'Status',
            'created-by' => 'Criado por',
            'created-at' => 'Criado em',
            'updated-at' => 'Atualizado em',
        ],

        'filters' => [
            'name' => 'Nome',
            'created-by' => 'Criado por',
            'updated-at' => 'Atualizado em',
            'created-at' => 'Criado em',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Origem atualizada',
                    'body' => 'A origem foi atualizada com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Origem excluída',
                    'body' => 'A origem foi excluída com sucesso.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Origens excluídas',
                    'body' => 'As origens foram excluídas com sucesso.',
                ],
            ],
        ],

        'empty-state-action' => [
            'create' => [
                'notification' => [
                    'title' => 'Origem criada',
                    'body' => 'A origem foi criada com sucesso.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'name' => 'Nome',
    ],
];
