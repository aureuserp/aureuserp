<?php

return [
    'title' => 'Tags',

    'navigation' => [
        'title' => 'Tags',
        'group' => 'Candidaturas',
    ],

    'form' => [
        'fields' => [
            'name' => 'Nome',
            'name-placeholder' => 'Informe o nome das tags',
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
            'employee' => 'Funcionário',
            'created-by' => 'Criado por',
            'updated-at' => 'Atualizado em',
            'created-at' => 'Criado em',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Tags atualizadas',
                    'body' => 'As tags foram atualizadas com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Tags excluídas',
                    'body' => 'As tags foram excluídas com sucesso.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Categorias de candidatos excluídas',
                    'body' => 'As categorias de candidatos foram excluídas com sucesso.',
                ],
            ],
        ],

        'empty-state-action' => [
            'create' => [
                'notification' => [
                    'title' => 'Tags criadas',
                    'body' => 'As tags foram criadas com sucesso.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'name' => 'Nome',
        'color' => 'Cor',
    ],
];
