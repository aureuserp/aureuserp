<?php

return [
    'navigation' => [
        'title' => 'Tags',
    ],

    'form' => [
        'name' => 'Nome',
        'color' => 'Cor',
    ],

    'table' => [
        'columns' => [
            'name' => 'Nome',
            'color' => 'Cor',
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

            'restore' => [
                'notification' => [
                    'title' => 'Tag restaurada',
                    'body' => 'A tag foi restaurada com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Tag excluída',
                    'body' => 'A tag foi excluída com sucesso.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Tag excluída permanentemente',
                    'body' => 'A tag foi excluída permanentemente com sucesso.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Tags restauradas',
                    'body' => 'As tags foram restauradas com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Tags excluídas',
                    'body' => 'As tags foram excluídas com sucesso.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Tags excluídas permanentemente',
                    'body' => 'As tags foram excluídas permanentemente com sucesso.',
                ],
            ],
        ],
    ],
];
