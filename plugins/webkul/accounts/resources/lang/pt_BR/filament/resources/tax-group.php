<?php

return [
    'form' => [
        'sections' => [
            'fields' => [
                'company' => 'Empresa',
                'country' => 'País',
                'name' => 'Nome',
                'preceding-subtotal' => 'Subtotal anterior',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'company' => 'Empresa',
            'country' => 'País',
            'created-by' => 'Criado por',
            'name' => 'Nome',
            'preceding-subtotal' => 'Subtotal anterior',
            'created-at' => 'Criado em',
            'updated-at' => 'Atualizado em',
        ],

        'groups' => [
            'name' => 'Nome',
            'company' => 'Empresa',
            'country' => 'País',
            'created-by' => 'Criado por',
            'created-at' => 'Criado em',
            'updated-at' => 'Atualizado em',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Grupo de impostos excluído',
                        'body' => 'O grupo de impostos foi excluído com sucesso.',
                    ],

                    'error' => [
                        'title' => 'Grupo de impostos não pôde ser excluído',
                        'body' => 'O grupo de impostos não pode ser excluído porque está em uso no momento.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Grupos de impostos excluídos',
                        'body' => 'Os grupos de impostos foram excluídos com sucesso.',
                    ],

                    'error' => [
                        'title' => 'Grupos de impostos não puderam ser excluídos',
                        'body' => 'Os grupos de impostos não podem ser excluídos porque estão em uso no momento.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'entries' => [
                'company' => 'Empresa',
                'country' => 'País',
                'name' => 'Nome',
                'preceding-subtotal' => 'Subtotal anterior',
            ],
        ],
    ],
];
