<?php

return [
    'form' => [
        'fields' => [
            'color' => 'Cor',
            'country' => 'País',
            'applicability' => 'Aplicabilidade',
            'name' => 'Nome',
            'status' => 'Status',
            'tax-negate' => 'Negar imposto',
        ],
    ],

    'table' => [
        'columns' => [
            'color' => 'Cor',
            'country' => 'País',
            'created-by' => 'Criado por',
            'applicability' => 'Aplicabilidade',
            'name' => 'Nome',
            'status' => 'Status',
            'tax-negate' => 'Negar imposto',
            'created-at' => 'Criado em',
            'updated-at' => 'Atualizado em',
            'deleted-at' => 'Excluído em',
        ],

        'filters' => [
            'bank' => 'Banco',
            'account-holder' => 'Titular da conta',
            'creator' => 'Criador',
            'can-send-money' => 'Pode enviar dinheiro',
        ],

        'groups' => [
            'country' => 'País',
            'created-by' => 'Criado por',
            'applicability' => 'Aplicabilidade',
            'name' => 'Nome',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Etiqueta de conta atualizada',
                    'body' => 'A etiqueta de conta foi atualizada com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Etiqueta de conta excluída',
                    'body' => 'A etiqueta de conta foi excluída com sucesso.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Etiquetas de conta excluídas',
                    'body' => 'As etiquetas de conta foram excluídas com sucesso.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'color' => 'Cor',
            'country' => 'País',
            'applicability' => 'Aplicabilidade',
            'name' => 'Nome',
            'status' => 'Status',
            'tax-negate' => 'Negar imposto',
        ],
    ],
];
