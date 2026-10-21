<?php

return [
    'label'        => 'Entrada/Saída de caixa',

    'form' => [
        'fields' => [
            'type'   => 'Tipo',
            'amount' => 'Valor',
            'reason' => 'Motivo',
        ],
    ],

    'notification' => [
        'success' => [
            'title' => 'Movimento de caixa registrado',
            'body'  => 'O saldo da gaveta de dinheiro foi atualizado.',
        ],
    ],
];
