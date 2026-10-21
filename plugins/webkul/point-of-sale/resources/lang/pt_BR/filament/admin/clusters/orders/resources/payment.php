<?php

return [
    'navigation' => [
        'title' => 'Pagamentos',
        'group' => 'Ponto de venda',
    ],

    'table' => [
        'columns' => [
            'order'           => 'Pedido',
            'session'         => 'Sessão',
            'payment-method'  => 'Método',
            'amount'          => 'Valor',
            'partner'         => 'Cliente',
            'is-change'       => 'Troco',
            'terminal-status' => 'Status do terminal',
            'paid-at'         => 'Pago em',
        ],

        'groups' => [
            'payment-method' => 'Método',
            'session'        => 'Sessão',
        ],

        'filters' => [
            'payment-method' => 'Método',
            'session'        => 'Sessão',
        ],
    ],
];
