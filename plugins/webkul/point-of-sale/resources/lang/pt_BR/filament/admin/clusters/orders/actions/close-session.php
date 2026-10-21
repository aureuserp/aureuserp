<?php

return [
    'label'        => 'Fechar sessão',

    'form' => [
        'fields' => [
            'cash-balance-end-real'             => 'Dinheiro contado',
            'cash-balance-end-real-helper-text' => 'Saldo esperado: :expected',
            'closing-notes'                     => 'Observações',
            'balancing-account'                 => 'Conta de balanceamento',
            'balancing-account-helper-text'     => 'Usada apenas quando o lançamento de fechamento não fecha.',
        ],
    ],

    'notification' => [
        'unbalanced' => [
            'title' => 'Lançamento de fechamento desbalanceado',
        ],

        'success' => [
            'title' => 'Sessão fechada',
            'body'  => 'A sessão foi fechada.',
        ],
    ],
];
