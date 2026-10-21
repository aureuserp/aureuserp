<?php

return [
    'title'        => 'Session',
    'plural-title' => 'Sessions',

    'log-attributes' => [
        'name'                  => 'Session',
        'state'                 => 'Statut',
        'cash-balance-start'    => "Solde d'ouverture",
        'cash-balance-end-real' => 'Solde compté',
        'cash-difference'       => 'Écart',
    ],

    'chatter' => [
        'opening' => [
            'difference' => "Écart de caisse à l'ouverture : :amount",
            'expected'   => "Espèces attendues à l'ouverture : :amount",
            'counted'    => "Espèces comptées à l'ouverture : :amount",
            'message'    => "Message du contrôle d'ouverture : :message",
        ],

        'closing' => [
            'difference' => 'Écart à la clôture : :amount',
            'expected'   => 'Montant attendu à la clôture : :amount',
            'counted'    => 'Montant compté à la clôture : :amount',
            'message'    => 'Message du contrôle de clôture : :message',
        ],

        'cash-movement' => [
            'in'     => "Entrée d'espèces : :amount",
            'out'    => "Sortie d'espèces : :amount",
            'reason' => 'Motif : :reason',
        ],
    ],
];
