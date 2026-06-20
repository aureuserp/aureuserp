<?php

return [
    'form' => [
        'fields' => [
            'code' => 'Código',
            'name' => 'Nome',
        ],
    ],

    'table' => [
        'columns' => [
            'code' => 'Código',
            'name' => 'Nome',
            'created-by' => 'Criado por',
        ],

        'groups' => [
            'code' => 'Código',
            'name' => 'Nome',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Incoterm atualizado',
                    'body' => 'O incoterm foi atualizado com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Incoterm excluído',
                    'body' => 'O incoterm foi excluído com sucesso.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Incoterm restaurado',
                    'body' => 'O incoterm foi restaurado com sucesso.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Incoterms restaurados',
                    'body' => 'Os incoterms foram restaurados com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Incoterms excluídos',
                    'body' => 'Os incoterms foram excluídos com sucesso.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Incoterms excluídos permanentemente',
                    'body' => 'Os incoterms foram excluídos permanentemente com sucesso.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'name' => 'Nome',
            'code' => 'Código',
        ],
    ],
];
