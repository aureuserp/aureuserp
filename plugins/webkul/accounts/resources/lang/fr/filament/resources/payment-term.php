<?php

return [
    'form' => [
        'sections' => [
            'fields' => [
                'payment-term'         => 'Condition de paiement',
                'company'              => 'Société',
                'company-placeholder'  => 'Toutes les sociétés',
                'early-discount'       => 'Escompte anticipé',
                'discount-days-prefix' => 'si payé sous',
                'discount-days-suffix' => 'jours',
                'reduced-tax'          => 'Taxe réduite',
                'note'                 => 'Note',
                'status'               => 'Statut',
            ],
        ],

        'tabs' => [
            'due-terms' => [
                'title' => 'Termes d\'échéance',

                'repeater' => [
                    'due-terms' => [
                        'fields' => [
                            'value'                  => 'Valeur',
                            'due'                    => 'Échéance',
                            'delay-type'             => 'Type de délai',
                            'days-on-the-next-month' => 'Jours du mois suivant',
                            'days'                   => 'Jours',
                        ],
                    ],
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'payment-term' => 'Condition de paiement',
            'company'      => 'Société',
            'created-at'   => 'Créé le',
            'updated-at'   => 'Mis à jour le',
        ],

        'groups' => [
            'company-name'        => 'Nom de la société',
            'discount-days'       => 'Jours d\'escompte',
            'early-pay-discount'  => 'Escompte pour paiement anticipé',
            'payment-term'        => 'Condition de paiement',
            'display-on-invoice'  => 'Afficher sur la facture',
            'early-discount'      => 'Escompte anticipé',
            'discount-percentage' => 'Pourcentage d\'escompte',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Condition de paiement restaurée',
                    'body'  => 'La condition de paiement a été restaurée avec succès.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Condition de paiement supprimée',
                    'body'  => 'La condition de paiement a été supprimée avec succès.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Condition de paiement définitivement supprimée',
                        'body'  => 'La condition de paiement a été définitivement supprimée avec succès.',
                    ],

                    'error' => [
                        'title' => 'Échec de la suppression définitive de la condition de paiement',
                        'body'  => 'La condition de paiement n\'a pas pu être définitivement supprimée car elle est associée à des écritures comptables.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Conditions de paiement restaurées',
                    'body'  => 'Les conditions de paiement ont été restaurées avec succès.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Conditions de paiement supprimées',
                    'body'  => 'Les conditions de paiement ont été supprimées avec succès.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Conditions de paiement définitivement supprimées',
                        'body'  => 'Les conditions de paiement ont été définitivement supprimées avec succès.',
                    ],

                    'error' => [
                        'title' => 'Échec de la suppression définitive des conditions de paiement',
                        'body'  => 'Les conditions de paiement n\'ont pas pu être définitivement supprimées car elles sont associées à des écritures comptables.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'entries' => [
                'payment-term'         => 'Condition de paiement',
                'early-discount'       => 'Escompte anticipé',
                'discount-percentage'  => 'Pourcentage d\'escompte',
                'discount-days-prefix' => 'si payé sous',
                'discount-days-suffix' => 'jours',
                'reduced-tax'          => 'Taxe réduite',
                'note'                 => 'Note',
                'status'               => 'Statut',
            ],
        ],

        'tabs' => [
            'due-terms' => [
                'title' => 'Termes d\'échéance',

                'repeater' => [
                    'due-terms' => [
                        'entries' => [
                            'value'                  => 'Valeur',
                            'due'                    => 'Échéance',
                            'delay-type'             => 'Type de délai',
                            'days-on-the-next-month' => 'Jours du mois suivant',
                            'days'                   => 'Jours',
                        ],
                    ],
                ],
            ],
        ],
    ],
];
