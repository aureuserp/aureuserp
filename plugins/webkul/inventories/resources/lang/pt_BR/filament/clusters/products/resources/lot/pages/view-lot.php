<?php

return [
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
