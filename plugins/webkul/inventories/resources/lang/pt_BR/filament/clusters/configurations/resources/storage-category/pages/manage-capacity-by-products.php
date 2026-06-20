<?php

return [
    'title' => 'Capacidade por produtos',

    'form' => [
        'product' => 'Produto',
        'qty' => 'Quantidade',
    ],

    'table' => [
        'header-actions' => [
            'create' => [
                'label' => 'Adicionar capacidade por produto',

                'notification' => [
                    'title' => 'Capacidade por produto criada',
                    'body' => 'A capacidade por produto foi adicionada com sucesso.',
                ],
            ],
        ],

        'columns' => [
            'product' => 'Produto',
            'qty' => 'Quantidade',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Capacidade por produto atualizada',
                    'body' => 'A capacidade por produto foi atualizada com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Capacidade por produto excluída',
                    'body' => 'A capacidade por produto foi excluída com sucesso.',
                ],
            ],
        ],
    ],
];
