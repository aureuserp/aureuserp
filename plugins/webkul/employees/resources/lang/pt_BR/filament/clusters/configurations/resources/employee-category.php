<?php

return [
    'title' => 'Tags',

    'navigation' => [
        'title' => 'Tags',
        'group' => 'Funcionário',
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
            'name-placeholder' => 'Informe o nome da tag',
            'color' => 'Cor',
        ],
    ],

    'table' => [
        'columns' => [
            'id' => 'ID',
            'name' => 'Nome',
            'color' => 'Cor',
            'created-by' => 'Criado por',
            'created-at' => 'Criado em',
            'updated-at' => 'Atualizado em',
        ],

        'filters' => [
            'name' => 'Nome',
            'created-by' => 'Criado por',
            'updated-by' => 'Atualizado por',
            'updated-at' => 'Atualizado em',
            'created-at' => 'Criado em',
        ],

        'groups' => [
            'name' => 'Nome',
            'job-position' => 'Cargo',
            'color' => 'Cor',
            'created-by' => 'Criado por',
            'created-at' => 'Criado em',
            'updated-at' => 'Atualizado em',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Tag atualizada',
                    'body' => 'A tag foi atualizada com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Tag excluída',
                    'body' => 'A tag foi excluída com sucesso.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Tags excluídas',
                    'body' => 'As tags foram excluídas com sucesso.',
                ],
            ],
        ],

        'empty-state-action' => [
            'create' => [
                'notification' => [
                    'title' => 'Tag criada',
                    'body' => 'A tag foi criada com sucesso.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'name' => 'Nome',
        'color' => 'Cor',
    ],
];
