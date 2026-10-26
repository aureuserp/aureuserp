<?php

return [
    'model-label'        => 'Piste',
    'plural-model-label' => 'Pistes',

    'navigation' => [
        'title' => 'Pistes',
    ],

    'views' => [
        'kanban' => 'Kanban',
        'list'   => 'Liste',
        'grid'   => 'Grille',
    ],

    'global-search' => [
        'contact' => 'Contact',
        'company' => 'Société',
    ],

    'form' => [
        'tabs' => [
            'notes'      => 'Notes',
            'extra-info' => 'Informations complémentaires',
        ],

        'fieldsets' => [
            'marketing' => 'Marketing',
            'analysis'  => 'Analyse',
        ],

        'fields' => [
            'name-placeholder'    => 'ex. Tarification produit',
            'probability'         => 'Probabilité',
            'company-name'        => 'Nom de la société',
            'address'             => 'Adresse',
            'street1'             => 'Rue...',
            'street2'             => 'Rue 2...',
            'city'                => 'Ville',
            'zip'                 => 'Code postal',
            'state'               => 'État',
            'country'             => 'Pays',
            'website'             => 'Site web',
            'website-placeholder' => 'ex. https://www.example.com',
            'salesperson'         => 'Vendeur',
            'team'                => 'Équipe commerciale',
            'contact-name'        => 'Nom du contact',
            'email'               => 'E-mail',
            'job-position'        => 'Poste',
            'phone'               => 'Téléphone',
            'priority'            => 'Priorité',
            'tags'                => 'Étiquettes',
            'company'             => 'Société',
            'campaign'            => 'Campagne',
            'medium'              => 'Support',
            'source'              => 'Source',
            'referred-by'         => 'Recommandé par',
            'assignment-date'     => 'Date d\'assignation',
            'closed-date'         => 'Date de clôture',
        ],
    ],

    'infolist' => [
        'sections' => [
            'lost' => 'Perdue',
        ],

        'entries' => [
            'lost-reason'  => 'Motif de perte',
            'closing-note' => 'Note de clôture',
        ],
    ],

    'table' => [
        'columns' => [
            'name'         => 'Piste',
            'contact-name' => 'Nom du contact',
            'company-name' => 'Nom de la société',
            'email'        => 'E-mail',
            'phone'        => 'Téléphone',
            'city'         => 'Ville',
            'country'      => 'Pays',
            'salesperson'  => 'Vendeur',
            'team'         => 'Équipe commerciale',
            'priority'     => 'Priorité',
            'tags'         => 'Étiquettes',
            'source'       => 'Source',
            'lost-reason'  => 'Motif de perte',
            'created-at'   => 'Créé le',
        ],
    ],

    'actions' => [
        'create' => [
            'label' => 'Nouvelle piste',
        ],

        'open' => [
            'label' => 'Ouvrir',
        ],

        'schedule-activity' => [
            'label' => 'Planifier une activité',
        ],

        'convert' => [
            'label' => 'Convertir en opportunité',

            'modal' => [
                'heading' => 'Convertir en opportunité',
                'submit'  => 'Créer l\'opportunité',
            ],

            'form' => [
                'action'     => 'Action de conversion',
                'duplicates' => 'Pistes et opportunités similaires à fusionner',

                'action-options' => [
                    'convert' => 'Convertir en opportunité',
                    'merge'   => 'Fusionner avec des opportunités existantes',
                ],

                'customer'          => 'Client',
                'existing-customer' => 'Client existant',
                'salesperson'       => 'Vendeur',
                'team'              => 'Équipe commerciale',

                'customer-options' => [
                    'create' => 'Créer un nouveau client',
                    'link'   => 'Lier à un client existant',
                    'none'   => 'Ne pas lier à un client',
                ],
            ],

            'notification' => [
                'title' => 'Convertie en opportunité',
                'body'  => '« :name » est désormais une opportunité du pipeline.',

                'invalid' => [
                    'title' => 'Sélection invalide',
                    'body'  => 'Le client, le vendeur ou l\'équipe commerciale sélectionné(e) n\'appartient pas à la société de cette piste.',
                ],
            ],
        ],

        'mass-convert' => [
            'label' => 'Convertir en opportunités',

            'modal' => [
                'heading' => 'Convertir en opportunités',
                'submit'  => 'Convertir',
            ],

            'form' => [
                'deduplicate'             => 'Appliquer la déduplication',
                'deduplicate-helper'      => 'Fusionner chaque piste avec ses pistes et opportunités similaires (même e-mail, téléphone ou client).',
                'salespeople'             => 'Vendeurs',
                'salespeople-helper'      => 'Les pistes sans vendeur leur sont réparties à tour de rôle.',
                'force-assignment'        => 'Forcer l\'attribution',
                'force-assignment-helper' => 'Réattribuer aussi les pistes qui ont déjà un vendeur.',

                'customer-options' => [
                    'create' => 'Lier à un client existant ou en créer un',
                    'link'   => 'Lier à un client existant',
                ],
            ],

            'notification' => [
                'title' => 'Pistes converties',
                'body'  => ':count piste(s) convertie(s), :merged enregistrement(s) similaire(s) fusionné(s), :skipped ignorée(s).',
            ],
        ],
        'lost' => [
            'notification' => [
                'title' => 'Piste perdue',
                'body'  => 'La piste a été marquée comme perdue et déplacée dans la vue Perdues.',
            ],
        ],

        'restore' => [
            'notification' => [
                'title' => 'Piste restaurée',
                'body'  => 'La piste a été restaurée.',
            ],
        ],

        'force-delete' => [
            'notification' => [
                'title' => 'Piste supprimée',
                'body'  => 'La piste a été définitivement supprimée.',
            ],
        ],
    ],
];
