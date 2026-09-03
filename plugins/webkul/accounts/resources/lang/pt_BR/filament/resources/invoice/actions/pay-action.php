<?php

return [
    'title' => 'Pagar',

    'form' => [
        'fields' => [
            'journal'                => 'Diário',
            'amount'                 => 'Valor',
            'currency'               => 'Moeda',
            'payment-method-line'    => 'Linha do método de pagamento',
            'payment-date'           => 'Data do pagamento',
            'recipient-bank-account' => 'Conta bancária do destinatário',
            'communication'          => 'Memorando',
        ],
    ],

    'notifications' => [
        'payment-failed' => [
            'title' => 'Falha no pagamento',
        ],
    ],
];
