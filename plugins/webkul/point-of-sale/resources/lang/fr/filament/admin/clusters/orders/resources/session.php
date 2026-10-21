<?php

return [

    'navigation' => [
        'title' => 'Sessions',
        'group' => 'Point de vente',
    ],

    'infolist' => [
        'section' => [
            'general' => [
                'title'   => 'Session',
                'entries' => [
                    'session'          => 'Session',
                    'opened-by'        => 'Ouverte par',
                    'point-of-sale'    => 'Point de vente',
                    'opening-date'     => 'Date d\'ouverture',
                    'starting-balance' => 'Solde initial',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'                  => 'ID de session',
            'config'                => 'Point de vente',
            'user'                  => 'Ouverte par',
            'started-at'            => 'Date d\'ouverture',
            'stopped-at'            => 'Date de clôture',
            'cash-balance-start'    => 'Solde initial',
            'cash-balance-end-real' => 'Solde final',
            'cash-balance-end'      => 'Solde final théorique',
            'order-count'           => 'Commandes',
            'total-payments-amount' => 'Paiements',
            'cash-difference'       => 'Écart',
            'is-rescue'             => 'Secours',
            'has-failed-operations' => 'Opérations échouées',
            'state'                 => 'Statut',
            'company'               => 'Société',
        ],

        'groups' => [
            'config'     => 'Point de vente',
            'state'      => 'Statut',
            'started-at' => 'Ouverte le',
        ],

        'filters' => [
            'state'  => 'Statut',
            'config' => 'Point de vente',
        ],
    ],
];
