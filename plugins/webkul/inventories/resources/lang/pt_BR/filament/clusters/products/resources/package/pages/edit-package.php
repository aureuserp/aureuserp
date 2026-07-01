<?php

return [
    'notification' => [
        'title' => 'Embalagem atualizada',
        'body' => 'A embalagem foi atualizada com sucesso.',
    ],

    'header-actions' => [
        'print' => [
            'label' => 'Imprimir',

            'actions' => [
                'without-content' => [
                    'label' => 'Imprimir código de barras',
                ],

                'with-content' => [
                    'label' => 'Imprimir código de barras com conteúdo',
                ],
            ],
        ],

        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Embalagem excluída',
                    'body' => 'A embalagem foi excluída com sucesso.',
                ],

                'error' => [
                    'title' => 'Não foi possível excluir a embalagem',
                    'body' => 'A embalagem não pode ser excluída porque está em uso.',
                ],
            ],
        ],
    ],
];
