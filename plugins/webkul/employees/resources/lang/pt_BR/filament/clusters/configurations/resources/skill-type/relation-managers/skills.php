<?php

return [
    'form' => [
        'name' => 'Nome',
    ],

    'table' => [
        'columns' => [
            'name' => 'Nome',
            'created-at' => 'Criado em',
            'updated-at' => 'Atualizado em',
        ],

        'groups' => [
            'created-at' => 'Criado em',
        ],

        'filters' => [
            'deleted-records' => 'Registros excluídos',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Habilidade atualizada',
                    'body' => 'A habilidade foi atualizada com sucesso.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Habilidade restaurada',
                    'body' => 'A habilidade foi restaurada com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Habilidade excluída',
                    'body' => 'A habilidade foi excluída com sucesso.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Habilidades excluídas',
                    'body' => 'As habilidades foram excluídas com sucesso.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Habilidades excluídas permanentemente',
                    'body' => 'As habilidades foram excluídas permanentemente com sucesso.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Habilidades restauradas permanentemente',
                    'body' => 'As habilidades foram restauradas permanentemente com sucesso.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'name' => 'Nome',
        ],
    ],
];
