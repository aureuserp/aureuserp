<?php

return [
    'navigation' => [
        'title' => 'Paiements',
        'group' => 'Point de vente',
    ],

    'table' => [
        'columns' => [
            'order'           => 'Commande',
            'session'         => 'Session',
            'payment-method'  => 'Méthode',
            'amount'          => 'Montant',
            'partner'         => 'Client',
            'is-change'       => 'Monnaie',
            'terminal-status' => 'Statut du terminal',
            'paid-at'         => 'Payé le',
        ],

        'groups' => [
            'payment-method' => 'Méthode',
            'session'        => 'Session',
        ],

        'filters' => [
            'payment-method' => 'Méthode',
            'session'        => 'Session',
        ],
    ],
];
