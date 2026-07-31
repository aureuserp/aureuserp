<?php

return [
    'label' => 'Cancelar',

    'action' => [
        'notification' => [
            'warning' => [
                'receipts' => [
                    'title' => 'Não é possível cancelar o pedido',
                    'body' => 'O pedido não pode ser cancelado porque possui recebimentos já concluídos.',
                ],

                'bills' => [
                    'title' => 'Não é possível cancelar o pedido',
                    'body' => 'O pedido não pode ser cancelado. Primeiro você deve cancelar as faturas de fornecedor relacionadas.',
                ],
            ],

            'success' => [
                'title' => 'Pedido cancelado',
                'body' => 'O pedido foi cancelado com sucesso.',
            ],
        ],
    ],
];
