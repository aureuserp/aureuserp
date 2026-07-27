<?php

return [
    'navigation' => [
        'title' => 'Transferências internas',
        'group' => 'Transferências',
    ],

    'global-search' => [
        'origin' => 'Origem',
    ],

    'table' => [
        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Transferência interna excluída',
                        'body' => 'A transferência interna foi excluída com sucesso.',
                    ],

                    'error' => [
                        'title' => 'Não foi possível excluir a transferência interna',
                        'body' => 'A transferência interna não pode ser excluída porque está em uso.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Transferências internas excluídas',
                        'body' => 'As transferências internas foram excluídas com sucesso.',
                    ],

                    'error' => [
                        'title' => 'Não foi possível excluir as transferências internas',
                        'body' => 'As transferências internas não podem ser excluídas porque estão em uso.',
                    ],
                ],
            ],
        ],
    ],
];
