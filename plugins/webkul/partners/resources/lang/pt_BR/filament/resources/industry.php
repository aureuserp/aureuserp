<?php

return [
    'form' => [
        'name' => 'Nome',
        'full-name' => 'Nome completo',
    ],

    'table' => [
        'columns' => [
            'name' => 'Nome',
            'full-name' => 'Nome completo',
            'created-at' => 'Criado em',
            'updated-at' => 'Atualizado em',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Setor atualizado',
                    'body' => 'O setor foi atualizado com sucesso.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Setor restaurado',
                    'body' => 'O setor foi restaurado com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Setor excluído',
                    'body' => 'O setor foi excluído com sucesso.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Setor excluído permanentemente',
                    'body' => 'O setor foi excluído permanentemente com sucesso.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Setores restaurados',
                    'body' => 'Os setores foram restaurados com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Setores excluídos',
                    'body' => 'Os setores foram excluídos com sucesso.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Setores excluídos permanentemente',
                    'body' => 'Os setores foram excluídos permanentemente com sucesso.',
                ],
            ],
        ],
    ],
];
