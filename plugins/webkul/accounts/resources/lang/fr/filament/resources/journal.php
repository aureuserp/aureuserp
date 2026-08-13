<?php

return [
    'form' => [
        'tabs' => [
            'journal-entries' => [
                'title' => 'Écritures comptables',

                'field-set' => [
                    'accounting-information' => [
                        'title'  => 'Informations comptables',
                        'fields' => [
                            'dedicated-credit-note-sequence' => 'Séquence dédiée aux avoirs',
                            'dedicated-payment-sequence'     => 'Séquence dédiée aux paiements',
                            'sort-code-placeholder'          => 'Saisissez le code du journal',
                            'sort-code'                      => 'Tri',
                            'currency'                       => 'Devise',
                            'color'                          => 'Couleur',
                            'default-account'                => 'Compte par défaut',
                            'profit-account'                 => 'Compte de profit',
                            'loss-account'                   => 'Compte de perte',
                            'suspense-account'               => 'Compte d\'attente',
                            'bank-account'                   => 'Compte bancaire',
                        ],
                    ],

                    'bank-account-number' => [
                        'title' => 'Numéro de compte bancaire',
                    ],
                ],
            ],

            'incoming-payments' => [
                'title'            => 'Paiements entrants',
                'add-action-label' => 'Ajouter une ligne',

                'fields' => [
                    'payment-method'             => 'Mode de paiement',
                    'display-name'               => 'Nom d\'affichage',
                    'account-number'             => 'Comptes des encaissements en attente',
                    'relation-notes'             => 'Notes de relation',
                    'relation-notes-placeholder' => 'Saisissez les détails de la relation',
                ],
            ],

            'outgoing-payments' => [
                'title'            => 'Paiements sortants',
                'add-action-label' => 'Ajouter une ligne',

                'fields' => [
                    'payment-method'             => 'Mode de paiement',
                    'display-name'               => 'Nom d\'affichage',
                    'account-number'             => 'Comptes des décaissements en attente',
                    'relation-notes'             => 'Notes de relation',
                    'relation-notes-placeholder' => 'Saisissez les détails de la relation',
                ],
            ],

            'advanced-settings' => [
                'title'  => 'Paramètres avancés',

                'fields' => [
                    'allowed-accounts'       => 'Comptes autorisés',
                    'control-access'         => 'Contrôle d\'accès',
                    'payment-communication'  => 'Communication de paiement',
                    'auto-check-on-post'     => 'Vérification automatique à la comptabilisation',
                    'communication-type'     => 'Type de communication',
                    'communication-standard' => 'Norme de communication',
                ],
            ],
        ],

        'general' => [
            'title' => 'Informations générales',

            'fields' => [
                'name'    => 'Nom',
                'type'    => 'Type',
                'company' => 'Société',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'Nom',
            'type'       => 'Type',
            'code'       => 'Code',
            'currency'   => 'Devise',
            'created-by' => 'Créé par',
            'status'     => 'Statut',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Journal supprimé',
                        'body'  => 'Le journal a été supprimé avec succès.',
                    ],

                    'error' => [
                        'title' => 'Échec de la suppression du journal',
                        'body'  => 'Le journal ne peut pas être supprimé car il est actuellement utilisé.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Journal supprimé',
                        'body'  => 'Le journal a été supprimé avec succès.',
                    ],

                    'error' => [
                        'title' => 'Échec de la suppression des journaux',
                        'body'  => 'Les journaux ne peuvent pas être supprimés car ils sont actuellement utilisés.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'tabs' => [
            'journal-entries' => [
                'title' => 'Écritures comptables',

                'field-set' => [
                    'accounting-information' => [
                        'title'   => 'Informations comptables',

                        'entries' => [
                            'dedicated-credit-note-sequence' => 'Séquence dédiée aux avoirs',
                            'dedicated-payment-sequence'     => 'Séquence dédiée aux paiements',
                            'sort-code-placeholder'          => 'Saisissez le code du journal',
                            'sort-code'                      => 'Tri',
                            'currency'                       => 'Devise',
                            'color'                          => 'Couleur',
                            'default-account'                => 'Compte par défaut',
                            'profit-account'                 => 'Compte de profit',
                            'loss-account'                   => 'Compte de perte',
                            'suspense-account'               => 'Compte d\'attente',
                        ],
                    ],

                    'bank-account-number' => [
                        'title' => 'Numéro de compte bancaire',

                        'entries' => [
                            'account-number' => 'Numéro de compte',
                        ],
                    ],
                ],
            ],

            'incoming-payments' => [
                'title' => 'Paiements entrants',

                'entries' => [
                    'payment-method'             => 'Mode de paiement',
                    'display-name'               => 'Nom d\'affichage',
                    'account-number'             => 'Comptes des encaissements en attente',
                    'relation-notes'             => 'Notes de relation',
                    'relation-notes-placeholder' => 'Saisissez les détails de la relation',
                ],
            ],

            'outgoing-payments' => [
                'title' => 'Paiements sortants',

                'entries' => [
                    'payment-method'             => 'Mode de paiement',
                    'display-name'               => 'Nom d\'affichage',
                    'account-number'             => 'Comptes des décaissements en attente',
                    'relation-notes'             => 'Notes de relation',
                    'relation-notes-placeholder' => 'Saisissez les détails de la relation',
                ],
            ],

            'advanced-settings' => [
                'title'   => 'Paramètres avancés',

                'allowed-accounts' => [
                    'title' => 'Comptes autorisés',

                    'entries' => [
                        'allowed-accounts'       => 'Comptes autorisés',
                        'control-access'         => 'Contrôle d\'accès',
                        'auto-check-on-post'     => 'Vérification automatique à la comptabilisation',
                    ],
                ],

                'payment-communication'  => [
                    'title' => 'Communication de paiement',

                    'entries' => [
                        'communication-type'     => 'Type de communication',
                        'communication-standard' => 'Norme de communication',
                    ],
                ],
            ],
        ],

        'general' => [
            'title' => 'Informations générales',

            'entries' => [
                'name'    => 'Nom',
                'type'    => 'Type',
                'company' => 'Société',
            ],
        ],
    ],

];
