<?php

return [
    'header-actions' => [
        'print' => [
            'label' => 'Imprimir',
        ],

        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Pedido excluído',
                    'body' => 'O pedido foi excluído com sucesso.',
                ],

                'error' => [
                    'title' => 'Não foi possível excluir o pedido',
                    'body' => 'O pedido não pode ser excluído porque está em uso.',
                ],
            ],
        ],
    ],
];
