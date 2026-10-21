<?php

return [

    'navigation' => [
        'title' => 'Sessões',
        'group' => 'Ponto de venda',
    ],

    'infolist' => [
        'section' => [
            'general' => [
                'title'   => 'Sessão',
                'entries' => [
                    'session'          => 'Sessão',
                    'opened-by'        => 'Aberta por',
                    'point-of-sale'    => 'Ponto de venda',
                    'opening-date'     => 'Data de abertura',
                    'starting-balance' => 'Saldo inicial',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'                  => 'ID da sessão',
            'config'                => 'Ponto de venda',
            'user'                  => 'Aberta por',
            'started-at'            => 'Data de abertura',
            'stopped-at'            => 'Data de fechamento',
            'cash-balance-start'    => 'Saldo inicial',
            'cash-balance-end-real' => 'Saldo final',
            'cash-balance-end'      => 'Saldo final teórico',
            'order-count'           => 'Pedidos',
            'total-payments-amount' => 'Pagamentos',
            'cash-difference'       => 'Diferença',
            'is-rescue'             => 'Resgate',
            'has-failed-operations' => 'Operações com falha',
            'state'                 => 'Status',
            'company'               => 'Empresa',
        ],

        'groups' => [
            'config'     => 'Ponto de venda',
            'state'      => 'Status',
            'started-at' => 'Aberta em',
        ],

        'filters' => [
            'state'  => 'Status',
            'config' => 'Ponto de venda',
        ],
    ],
];
