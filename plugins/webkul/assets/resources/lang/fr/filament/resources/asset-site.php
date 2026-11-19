<?php

return [
    'title'        => 'Site d\'actifs et de gardes',
    'plural-title' => 'Sites d\'actifs et de gardes',

    'navigation' => [
        'title' => 'Sites d\'actifs et de gardes',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Détails du site',
                'fields' => [
                    'name'              => 'Nom',
                    'parent'            => 'Site parent',
                    'usage-type'        => 'Type d\'utilisation',
                    'usage-type-help'   => 'Les emplacements d\'actifs fixes accueillent les actifs déployés par l\'entreprise ; les zones de garde classent la garde personnelle des employés.',
                    'code'              => 'Code',
                    'code-help'         => 'Code interne optionnel, unique au sein de la société.',
                    'type'              => 'Type',
                    'type-name'         => 'Nom du type',
                    'type-icon'         => 'Icône',
                    'type-color'        => 'Couleur',
                    'note'              => 'Adresse / Emplacement et remarques',
                    'note-placeholder'  => 'Bâtiment, étage, zone de terrain ou autres détails d\'emplacement.',
                ],
            ],

            'settings' => [
                'title'  => 'Paramètres',
                'fields' => [
                    'company'                  => 'Société',
                    'default-custodian'        => 'Gardien par défaut du site',
                    'default-custodian-help'   => 'Employé automatiquement affecté comme récepteur de garde lors de l\'envoi d\'actifs sur ce site.',
                    'is-active'                => 'Actif',
                    'requires-custodian'       => 'Gardien requis',
                    'requires-custodian-help'  => 'Les sites de terrain et de véhicules nécessitent un récepteur désigné pour chaque remise de garde.',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'               => 'Nom',
            'full-name'          => 'Nom complet',
            'usage-type'         => 'Type d\'utilisation',
            'code'               => 'Code',
            'type'               => 'Type',
            'default-custodian'  => 'Gardien par défaut',
            'is-active'          => 'Actif',
            'requires-custodian' => 'Gardien requis',
            'assets-count'       => 'Actifs',
            'total-cost'         => 'Valorisation totale',
            'company'            => 'Société',
            'creator'            => 'Créé par',
            'deleted-at'         => 'Archivé le',
            'created-at'         => 'Créé le',
            'updated-at'         => 'Mis à jour le',
        ],

        'filters' => [
            'usage-type'         => 'Type d\'utilisation',
            'parent'             => 'Site parent',
            'type'               => 'Type',
            'is-active'          => 'Actif',
            'requires-custodian' => 'Gardien requis',
            'company'            => 'Société',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Site mis à jour',
                    'body'  => 'Le site a été mis à jour avec succès.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'error' => [
                        'title' => 'Impossible de supprimer le site',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'   => 'Détails du site',
                'entries' => [
                    'name'               => 'Nom',
                    'full-name'          => 'Nom complet',
                    'parent'             => 'Site parent',
                    'usage-type'         => 'Type d\'utilisation',
                    'code'               => 'Code',
                    'type'               => 'Type',
                    'company'            => 'Société',
                    'is-active'          => 'Actif',
                    'requires-custodian' => 'Gardien requis',
                    'default-custodian'  => 'Gardien par défaut',
                    'note'               => 'Adresse / Emplacement et remarques',
                ],
            ],

            'audit' => [
                'title'   => 'Audit',
                'entries' => [
                    'creator'    => 'Créé par',
                    'created-at' => 'Créé le',
                    'updated-at' => 'Mis à jour le',
                ],
            ],
        ],
    ],

    'relation-manager' => [
        'site-assets' => [
            'title'   => 'Contenu du site',
            'columns' => [
                'asset-tag'          => 'Étiquette d\'actif',
                'asset-name'         => 'Actif',
                'category'           => 'Catégorie',
                'current-site'       => 'Emplacement / Sous-site',
                'serial-no'          => 'N° de série',
                'receiver'           => 'Reçu par',
                'condition-out'      => 'État de sortie',
                'condition-in'       => 'État d\'entrée',
                'cost'               => 'Coût',
                'issued-at'          => 'Remis le',
                'expected-return-at' => 'Retour prévu',
                'status'             => 'Statut',
                'ended-type'         => 'Terminé comme',
                'issued-by'          => 'Remis par',
            ],
            'filters' => [
                'status' => 'Statut',
            ],
            'status' => [
                'open'   => 'Sur le site',
                'closed' => 'Retourné',
            ],
            'tabs' => [
                'all'           => 'Tous (sous-sites inclus)',
                'direct'        => 'Actifs directs',
                'subsites'      => 'Sous-sites',
                'with-children' => ':site (Tous)',
                'only'          => ':site (Direct uniquement)',
            ],
        ],
    ],

    'pages' => [
        'list' => [
            'header-actions' => [
                'create' => [
                    'label'        => 'Nouveau site d\'actif',
                    'notification' => [
                        'title' => 'Site créé',
                        'body'  => 'Le site a été créé avec succès.',
                    ],
                ],
            ],
            'views' => [
                'all'             => 'Tous les sites',
                'asset-locations' => 'Emplacements d\'actifs fixes',
                'custody-zones'   => 'Zones de garde',
                'archived'        => 'Sites archivés',
            ],
        ],
        'create' => [
            'notification' => [
                'title' => 'Site créé',
                'body'  => 'Le site a été créé avec succès.',
            ],
        ],
        'edit' => [
            'notification' => [
                'title' => 'Site mis à jour',
                'body'  => 'Le site a été mis à jour avec succès.',
            ],
        ],
    ],
];
