<?php

return [
    'title' => 'Gérer les comptes par défaut',

    'setup' => [
        'action'       => 'Configurer la comptabilité pour cette société',
        'notice'       => 'La comptabilité n\'est pas encore configurée pour cette société. Utilisez "Configurer la comptabilité" ci-dessus pour créer son plan comptable, ses journaux et ses paramètres par défaut.',
        'notification' => [
            'title' => 'Comptabilité configurée',
            'body'  => 'Le plan comptable, les journaux et les paramètres par défaut ont été créés pour cette société.',
        ],
    ],

    'form' => [
        'exchange-difference-entries' => [
            'label' => 'Écritures d\'écart de change',

            'fields' => [
                'journal' => [
                    'label' => 'Journal',
                ],

                'gain' => [
                    'label' => 'Gain',
                ],

                'loss' => [
                    'label' => 'Perte',
                ],
            ],
        ],

        'bank-transfer-and-payments' => [
            'label' => 'Virements bancaires et paiements',

            'fields' => [
                'bank-suspense-account' => [
                    'label' => 'Compte d\'attente bancaire',
                ],

                'transfer-account' => [
                    'label' => 'Compte de virement',
                ],
            ],
        ],

        'product-accounts' => [
            'label' => 'Comptes de produits',

            'fields' => [
                'income-account' => [
                    'label' => 'Compte de revenus',
                ],

                'expense-account' => [
                    'label' => 'Compte de charges',
                ],
            ],
        ],
    ],
];
