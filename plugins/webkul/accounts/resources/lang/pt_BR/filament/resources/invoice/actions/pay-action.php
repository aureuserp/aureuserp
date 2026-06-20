<?php

return [
    'title' => 'Pagar',

    'form' => [
        'fields' => [
            'journal' => 'Diário',
            'amount' => 'Valor',
            'currency' => 'Moeda',
            'payment-method-line' => 'Linha do método de pagamento',
            'payment-date' => 'Data do pagamento',
            'partner-bank-account' => 'Conta bancária do parceiro',
            'communication' => 'Memorando',
        ],
    ],

    'notifications' => [
        'payment-failed' => [
            'title' => 'Falha no pagamento',
        ],
    ],
];
