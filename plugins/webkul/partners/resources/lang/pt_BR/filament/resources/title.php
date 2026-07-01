<?php

return [
    'form' => [
        'name' => 'Nome',
        'short-name' => 'Nome curto',
    ],

    'table' => [
        'columns' => [
            'name' => 'Nome',
            'short-name' => 'Nome curto',
            'created-at' => 'Criado em',
            'updated-at' => 'Atualizado em',
        ],

        'filters' => [
            'creator' => 'Criador',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Título atualizado',
                    'body' => 'O título foi atualizado com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Título excluído',
                    'body' => 'O título foi excluído com sucesso.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Títulos excluídos',
                    'body' => 'Os títulos foram excluídos com sucesso.',
                ],
            ],
        ],
    ],
];
