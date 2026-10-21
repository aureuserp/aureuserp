<?php

return [
    'label'        => 'Clôturer la session',

    'form' => [
        'fields' => [
            'cash-balance-end-real'             => 'Espèces comptées',
            'cash-balance-end-real-helper-text' => 'Solde attendu : :expected',
            'closing-notes'                     => 'Notes',
            'balancing-account'                 => 'Compte d\'équilibrage',
            'balancing-account-helper-text'     => 'Utilisé uniquement lorsque l\'écriture de clôture n\'est pas équilibrée.',
        ],
    ],

    'notification' => [
        'unbalanced' => [
            'title' => 'Écriture de clôture déséquilibrée',
        ],

        'success' => [
            'title' => 'Session clôturée',
            'body'  => 'La session a été clôturée.',
        ],
    ],
];
