<?php

return [
    'model-label'        => 'Opportunité',
    'plural-model-label' => 'Opportunités',

    'navigation' => [
        'title' => 'Mon pipeline',
    ],

    'global-search' => [
        'customer' => 'Client',
        'stage'    => 'Étape',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Opportunité',

                'fields' => [
                    'name'             => 'Opportunité',
                    'name-placeholder' => 'ex. Tarification produit',
                    'expected-revenue' => 'Revenu attendu',
                    'probability'      => 'Probabilité',
                    'customer'         => 'Client',
                    'email'            => 'E-mail',
                    'phone'            => 'Téléphone',
                ],
            ],

            'notes' => [
                'title' => 'Notes internes',
            ],

            'sales' => [
                'title' => 'Ventes',

                'fields' => [
                    'salesperson'      => 'Vendeur',
                    'team'             => 'Équipe commerciale',
                    'company'          => 'Société',
                    'expected-closing' => 'Clôture prévue',
                    'priority'         => 'Priorité',
                    'tags'             => 'Étiquettes',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'lost' => [
                'title' => 'Perdue',

                'entries' => [
                    'lost-reason' => 'Motif de perte',
                    'feedback'    => 'Note de clôture',
                ],
            ],

            'general' => [
                'title' => 'Opportunité',

                'entries' => [
                    'expected-revenue' => 'Revenu attendu',
                    'probability'      => 'Probabilité',
                    'customer'         => 'Client',
                    'email'            => 'E-mail',
                    'phone'            => 'Téléphone',
                ],
            ],

            'notes' => [
                'title' => 'Notes internes',
            ],

            'sales' => [
                'title' => 'Ventes',

                'entries' => [
                    'salesperson'      => 'Vendeur',
                    'team'             => 'Équipe commerciale',
                    'company'          => 'Société',
                    'expected-closing' => 'Clôture prévue',
                    'priority'         => 'Priorité',
                    'tags'             => 'Étiquettes',
                    'closed-at'        => 'Clôturée le',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'             => 'Opportunité',
            'customer'         => 'Client',
            'stage'            => 'Étape',
            'expected-revenue' => 'Revenu attendu',
            'probability'      => 'Probabilité',
            'priority'         => 'Priorité',
            'salesperson'      => 'Vendeur',
            'team'             => 'Équipe commerciale',
            'expected-closing' => 'Clôture prévue',
            'tags'             => 'Étiquettes',
            'email'            => 'E-mail',
            'phone'            => 'Téléphone',
            'medium'           => 'Support',
            'lost-reason'      => 'Motif de perte',
            'created-at'       => 'Créé le',
        ],

        'summaries' => [
            'total' => 'Total',
        ],

        'groups' => [
            'stage'            => 'Étape',
            'team'             => 'Équipe commerciale',
            'salesperson'      => 'Vendeur',
            'priority'         => 'Priorité',
            'expected-closing' => 'Clôture prévue',
        ],

        'actions' => [
            'schedule-activity' => [
                'label' => 'Planifier une activité',
            ],

            'restore' => [
                'label' => 'Restaurer',

                'notification' => [
                    'title' => 'Opportunité restaurée',
                    'body'  => 'L\'opportunité a été restaurée dans le pipeline.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Opportunité supprimée',
                    'body'  => 'L\'opportunité a été définitivement supprimée.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Opportunités restaurées',
                    'body'  => 'Les opportunités ont été restaurées dans le pipeline.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Opportunités supprimées',
                    'body'  => 'Les opportunités ont été définitivement supprimées.',
                ],
            ],
        ],
    ],

    'actions' => [
        'new-quotation' => [
            'label'             => 'Nouveau devis',
            'modal-heading'     => 'Nouveau devis',
            'modal-description' => 'Cette opportunité n\'a pas encore de client. Choisissez le client du devis.',
            'submit'            => 'Continuer',
            'subheading'        => 'Depuis l\'opportunité : :name',
        ],

        'won' => [
            'label' => 'Gagnée',

            'modal' => [
                'heading'     => 'Marquer comme gagnée',
                'description' => 'L\'opportunité sera déplacée vers l\'étape gagnée avec une probabilité de 100 %.',
            ],

            'notification' => [
                'success' => [
                    'title' => 'Opportunité gagnée',
                    'body'  => 'L\'opportunité a été marquée comme gagnée.',
                ],

                'no-won-stage' => [
                    'title' => 'Aucune étape gagnée',
                    'body'  => 'Marquez d\'abord une étape comme « Étape gagnée ? » dans CRM → Configuration → Étapes.',
                ],
            ],
        ],

        'lost' => [
            'label' => 'Perdue',

            'modal' => [
                'heading' => 'Marquer comme perdue',
                'submit'  => 'Marquer comme perdue',
            ],

            'form' => [
                'lost-reason' => 'Motif de perte',
                'feedback'    => 'Note de clôture',
            ],

            'notification' => [
                'title' => 'Opportunité perdue',
                'body'  => 'L\'opportunité a été marquée comme perdue et déplacée dans la vue Perdues.',
            ],
        ],
    ],

    'quotations' => [
        'title' => 'Devis',

        'columns' => [
            'number'      => 'Numéro',
            'date'        => 'Date',
            'customer'    => 'Client',
            'salesperson' => 'Vendeur',
            'total'       => 'Total',
            'status'      => 'Statut',
        ],

        'empty' => [
            'heading'     => 'Aucun devis pour le moment',
            'description' => 'Utilisez « Nouveau devis » pour créer un devis depuis cette opportunité.',
        ],
    ],
];
