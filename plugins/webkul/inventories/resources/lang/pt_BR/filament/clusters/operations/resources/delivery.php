<?php

return [
    'navigation' => [
        'title' => 'Entregas',
        'group' => 'Transferências',
    ],

    'global-search' => [
        'partner' => 'Parceiro',
        'origin' => 'Origem',
    ],

    'table' => [
        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Entrega excluída',
                        'body' => 'A entrega foi excluída com sucesso.',
                    ],

                    'error' => [
                        'title' => 'Não foi possível excluir a entrega',
                        'body' => 'A entrega não pode ser excluída porque está em uso.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Entregas excluídas',
                        'body' => 'As entregas foram excluídas com sucesso.',
                    ],

                    'error' => [
                        'title' => 'Não foi possível excluir as entregas',
                        'body' => 'As entregas não podem ser excluídas porque estão em uso.',
                    ],
                ],
            ],
        ],
    ],
];
