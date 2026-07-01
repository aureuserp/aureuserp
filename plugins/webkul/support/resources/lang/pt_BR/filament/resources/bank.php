<?php

return [
    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Geral',

                'fields' => [
                    'name' => 'Nome',
                    'code' => 'Código identificador do banco',
                    'email' => 'E-mail',
                    'phone' => 'Telefone',
                ],
            ],

            'address' => [
                'title' => 'Endereço',

                'fields' => [
                    'address' => 'Endereço',
                    'city' => 'Cidade',
                    'street1' => 'Rua 1',
                    'street2' => 'Rua 2',
                    'state' => 'Estado',
                    'zip' => 'CEP',
                    'country' => 'País',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name' => 'Nome',
            'code' => 'Código identificador do banco',
            'country' => 'País',
            'created-at' => 'Criado em',
            'updated-at' => 'Atualizado em',
            'deleted-at' => 'Excluído em',
        ],

        'groups' => [
            'country' => 'País',
            'created-at' => 'Criado em',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Banco atualizado',
                    'body' => 'O banco foi atualizado com sucesso.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Banco restaurado',
                    'body' => 'O banco foi restaurado com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Banco excluído',
                    'body' => 'O banco foi excluído com sucesso.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Banco excluído permanentemente',
                    'body' => 'O banco foi excluído permanentemente com sucesso.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Bancos restaurados',
                    'body' => 'Os bancos foram restaurados com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Bancos excluídos',
                    'body' => 'Os bancos foram excluídos com sucesso.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Bancos excluídos permanentemente',
                    'body' => 'Os bancos foram excluídos permanentemente com sucesso.',
                ],
            ],
        ],
    ],
];
