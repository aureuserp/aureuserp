<?php

return [
    'title' => 'Graus',

    'navigation' => [
        'title' => 'Graus',
        'group' => 'Candidaturas',
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
            'name-placeholder' => 'Informe o nome do grau',
        ],
    ],

    'table' => [
        'columns' => [
            'id' => 'ID',
            'name' => 'Nome',
            'created-by' => 'Criado por',
            'created-at' => 'Criado em',
            'updated-at' => 'Atualizado em',
        ],

        'filters' => [
            'name' => 'Nome',
            'employee' => 'Funcionário',
            'created-by' => 'Criado por',
            'updated-at' => 'Atualizado em',
            'created-at' => 'Criado em',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Grau atualizado',
                    'body' => 'O grau foi atualizado com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Grau excluído',
                    'body' => 'O grau foi excluído com sucesso.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Graus excluídos',
                    'body' => 'Os graus foram excluídos com sucesso.',
                ],
            ],
        ],

        'empty-state-action' => [
            'create' => [
                'notification' => [
                    'title' => 'Grau criado',
                    'body' => 'O grau foi criado com sucesso.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'name' => 'Nome',
    ],
];
