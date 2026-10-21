<?php

return [
    'title'        => 'Sessão',
    'plural-title' => 'Sessões',

    'log-attributes' => [
        'name'                  => 'Sessão',
        'state'                 => 'Status',
        'cash-balance-start'    => 'Saldo de abertura',
        'cash-balance-end-real' => 'Saldo contado',
        'cash-difference'       => 'Diferença',
    ],

    'chatter' => [
        'opening' => [
            'difference' => 'Diferença de caixa na abertura: :amount',
            'expected'   => 'Caixa esperado na abertura: :amount',
            'counted'    => 'Caixa contado na abertura: :amount',
            'message'    => 'Mensagem do controle de abertura: :message',
        ],

        'closing' => [
            'difference' => 'Diferença no fechamento: :amount',
            'expected'   => 'Esperado no fechamento: :amount',
            'counted'    => 'Contado no fechamento: :amount',
            'message'    => 'Mensagem do controle de fechamento: :message',
        ],

        'cash-movement' => [
            'in'     => 'Entrada de caixa: :amount',
            'out'    => 'Saída de caixa: :amount',
            'reason' => 'Motivo: :reason',
        ],
    ],
];
