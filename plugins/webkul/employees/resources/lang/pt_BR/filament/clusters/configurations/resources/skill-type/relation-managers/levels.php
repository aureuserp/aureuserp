<?php

return [
    'form' => [
        'name' => 'Nome',
        'level' => 'Nível',
        'default-level' => 'Nível padrão',
    ],

    'table' => [
        'columns' => [
            'name' => 'Nome',
            'level' => 'Nível',
            'default-level' => 'Nível padrão',
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
            'create' => [
                'notification' => [
                    'title' => 'Nível de habilidade criado',
                    'body' => 'O nível de habilidade foi criado com sucesso.',
                ],
            ],

            'edit' => [
                'notification' => [
                    'title' => 'Nível de habilidade atualizado',
                    'body' => 'O nível de habilidade foi atualizado com sucesso.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Nível de habilidade restaurado',
                    'body' => 'O nível de habilidade foi restaurado com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Nível de habilidade excluído',
                    'body' => 'O nível de habilidade foi excluído com sucesso.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Níveis de habilidade excluídos',
                    'body' => 'As habilidades foram excluídas com sucesso.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Níveis de habilidade excluídos permanentemente',
                    'body' => 'As habilidades foram excluídas permanentemente com sucesso.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Níveis de habilidade restaurados permanentemente',
                    'body' => 'As habilidades foram restauradas permanentemente com sucesso.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'name' => 'Nome',
            'level' => 'Nível',
            'default-level' => 'Nível padrão',
        ],
    ],
];
