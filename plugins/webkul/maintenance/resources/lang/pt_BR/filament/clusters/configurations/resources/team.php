<?php

return [
    'navigation' => [
        'title' => 'Equipes',
    ],

    'form' => [
        'name' => 'Nome',
        'company' => 'Empresa',
        'users' => 'Membros da equipe',
    ],

    'table' => [
        'columns' => [
            'name' => 'Nome',
            'company' => 'Empresa',
            'users' => 'Membros da equipe',
            'created-at' => 'Criado em',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Equipe atualizada',
                    'body' => 'A equipe foi atualizada com sucesso.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Equipe restaurada',
                    'body' => 'A equipe foi restaurada com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Equipe excluída',
                    'body' => 'A equipe foi excluída com sucesso.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Equipe excluída permanentemente',
                        'body' => 'A equipe foi excluída permanentemente com sucesso.',
                    ],
                    'error' => [
                        'title' => 'Equipe não pôde ser excluída permanentemente',
                        'body' => 'A equipe está em uso e não pode ser excluída permanentemente.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Equipes restauradas',
                    'body' => 'As equipes foram restauradas com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Equipes excluídas',
                    'body' => 'As equipes foram excluídas com sucesso.',
                ],
            ],
        ],
    ],
];
