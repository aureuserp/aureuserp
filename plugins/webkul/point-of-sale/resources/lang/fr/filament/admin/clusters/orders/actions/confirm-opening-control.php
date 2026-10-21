<?php

return [
    'label'        => 'Ouvrir la session',

    'form' => [
        'fields' => [
            'cash-balance-start' => 'Solde d\'espèces d\'ouverture',
            'opening-notes'      => 'Notes',
        ],
    ],

    'notification' => [
        'success' => [
            'title' => 'Session ouverte',
            'body'  => 'La session est maintenant en cours.',
        ],
    ],
];
