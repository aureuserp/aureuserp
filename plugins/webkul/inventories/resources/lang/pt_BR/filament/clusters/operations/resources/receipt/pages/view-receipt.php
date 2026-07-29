<?php

return [
    'header-actions' => [
        'print' => [
            'label' => 'Imprimir',
        ],

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
];
