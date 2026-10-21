<?php

return [
    'label'          => 'Devolver produtos',
    'refunded-label' => 'Reembolsado',

    'form' => [
        'fields' => [
            'payment-method' => 'Método de pagamento do reembolso',
            'lines'          => 'Linhas a reembolsar',
            'selected'       => 'Reembolsar',
            'product'        => 'Produto',
            'refundable'     => 'Reembolsável',
            'quantity'       => 'Quantidade',
        ],
    ],

    'notification' => [
        'success' => [
            'title' => 'Reembolso criado',
            'body'  => 'Um pedido de reembolso foi criado para as linhas selecionadas.',
        ],
    ],
];
