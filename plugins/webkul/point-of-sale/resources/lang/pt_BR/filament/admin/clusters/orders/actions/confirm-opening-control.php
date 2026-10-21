<?php

return [
    'label'        => 'Abrir sessão',

    'form' => [
        'fields' => [
            'cash-balance-start' => 'Saldo de caixa de abertura',
            'opening-notes'      => 'Observações',
        ],
    ],

    'notification' => [
        'success' => [
            'title' => 'Sessão aberta',
            'body'  => 'A sessão está agora em andamento.',
        ],
    ],
];
