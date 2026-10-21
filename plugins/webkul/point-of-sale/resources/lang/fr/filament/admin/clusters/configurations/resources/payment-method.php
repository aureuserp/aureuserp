<?php

return [
    'navigation' => [
        'title' => 'Modes de paiement',
        'group' => 'Point de vente',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Général',

                'fields' => [
                    'name'                             => 'Nom',
                    'company'                          => 'Société',
                    'terminal-type'                    => 'Intégration',
                    'is-split-transaction'             => 'Identifier le client',
                    'is-split-transaction-helper-text' => 'Comptabiliser une écriture par paiement sur le compte client.',
                    'is-active'                        => 'Actif',
                ],
            ],

            'accounting' => [
                'title'  => 'Comptabilité',

                'fields' => [
                    'journal'             => 'Journal',
                    'journal-placeholder' => 'Laissez vide pour utiliser le compte client du client',
                    'account-placeholder' => 'Laissez vide pour utiliser le compte par défaut des paramètres de la société',
                    'receivable-account'  => 'Compte intermédiaire',
                    'outstanding-account' => 'Compte d\'attente',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'   => 'Général',

                'entries' => [
                    'name'                    => 'Nom',
                    'type'                    => 'Type',
                    'terminal-type'           => 'Terminal de paiement',
                    'journal-name'            => 'Journal',
                    'receivableAccount-name'  => 'Compte client',
                    'outstandingAccount-name' => 'Compte d\'attente',
                    'is-cash-count'           => 'Compté dans le tiroir-caisse',
                    'is-split-transaction'    => 'Diviser les transactions',
                    'is-active'               => 'Actif',
                    'company-name'            => 'Société',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'               => 'Nom',
            'type'               => 'Type',
            'journal'            => 'Journal',
            'configs'            => 'POS',
            'terminal-type'      => 'Intégration',
            'receivable-account' => 'Compte intermédiaire',
            'is-cash-count'      => 'Tiroir-caisse',
            'is-active'          => 'Actif',
            'company'            => 'Société',
        ],

        'groups' => [
            'type'    => 'Type',
            'company' => 'Société',
        ],

        'filters' => [
            'type' => 'Type',
        ],

        'record-actions' => [
            'restore' => [
                'notification' => [
                    'success' => [
                        'title' => 'Mode de paiement restauré',
                        'body'  => 'Le mode de paiement a été restauré.',
                    ],
                ],
            ],

            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Mode de paiement supprimé',
                        'body'  => 'Le mode de paiement a été supprimé.',
                    ],
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Mode de paiement supprimé définitivement',
                        'body'  => 'Le mode de paiement a été supprimé définitivement.',
                    ],

                    'error' => [
                        'title' => 'Impossible de supprimer le mode de paiement',
                        'body'  => 'Le mode de paiement ne peut pas être supprimé définitivement car il possède des paiements. Archivez-le plutôt.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Modes de paiement supprimés définitivement',
                        'body'  => 'Les modes de paiement sélectionnés ont été supprimés définitivement.',
                    ],

                    'error' => [
                        'title' => 'Impossible de supprimer les modes de paiement',
                        'body'  => 'Un ou plusieurs modes de paiement ne peuvent pas être supprimés définitivement car ils possèdent des paiements. Archivez-les plutôt.',
                    ],
                ],
            ],
        ],
    ],

    'in-open-session' => [
        'title' => 'Mode de paiement utilisé',
    ],
];
