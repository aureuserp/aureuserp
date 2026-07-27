<?php

return [
    'navigation' => [
        'title' => 'Recebimentos',
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
                        'title' => 'Recebimento excluído',
                        'body' => 'O recebimento foi excluído com sucesso.',
                    ],

                    'error' => [
                        'title' => 'Não foi possível excluir o recebimento',
                        'body' => 'O recebimento não pode ser excluído porque está em uso.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Recebimentos excluídos',
                        'body' => 'Os recebimentos foram excluídos com sucesso.',
                    ],

                    'error' => [
                        'title' => 'Não foi possível excluir os recebimentos',
                        'body' => 'Os recebimentos não podem ser excluídos porque estão em uso.',
                    ],
                ],
            ],
        ],
    ],
];
