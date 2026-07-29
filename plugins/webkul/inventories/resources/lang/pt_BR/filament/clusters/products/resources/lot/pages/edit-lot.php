<?php

return [
    'notification' => [
        'title' => 'Lote atualizado',
        'body' => 'O lote foi atualizado com sucesso.',
    ],

    'header-actions' => [
        'print' => [
            'label' => 'Imprimir',
        ],

        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Lote excluído',
                    'body' => 'O lote foi excluído com sucesso.',
                ],

                'error' => [
                    'title' => 'Não foi possível excluir o lote',
                    'body' => 'O lote não pode ser excluído porque está em uso.',
                ],
            ],
        ],
    ],
];
