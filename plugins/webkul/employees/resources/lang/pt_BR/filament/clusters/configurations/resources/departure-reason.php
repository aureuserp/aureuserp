<?php

return [
    'title' => 'Motivos de saída',

    'navigation' => [
        'title' => 'Motivos de saída',
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
                    'title' => 'Motivo de saída atualizado',
                    'body' => 'O motivo de saída foi atualizado com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Motivo de saída excluído',
                    'body' => 'O motivo de saída foi excluído com sucesso.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Motivos de saída excluídos',
                    'body' => 'Os motivos de saída foram excluídos com sucesso.',
                ],
            ],
        ],

        'empty-state-action' => [
            'create' => [
                'notification' => [
                    'title' => 'Motivo de saída criado',
                    'body' => 'O motivo de saída foi criado com sucesso.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'name' => 'Nome',
    ],
];
