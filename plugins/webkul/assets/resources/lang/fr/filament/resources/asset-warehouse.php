<?php

return [
    'title'        => 'Entrepôt d\'actifs',
    'plural-title' => 'Entrepôts d\'actifs',

    'navigation' => [
        'title' => 'Entrepôts',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Détails de l\'entrepôt',
                'fields' => [
                    'name'             => 'Nom',
                    'code'             => 'Code',
                    'code-help'        => 'Code interne facultatif, unique au sein de l\'entreprise.',
                    'note'             => 'Adresse / Emplacement et remarques',
                    'note-placeholder' => 'Remarques sur l\'emplacement ou le stockage.',
                ],
            ],

            'settings' => [
                'title'  => 'Paramètres',
                'fields' => [
                    'company'        => 'Entreprise',
                    'custodian'      => 'Gestionnaire d\'entrepôt',
                    'custodian-help' => 'Employé responsable des actifs stockés dans cet entrepôt.',
                    'is-active'      => 'Actif',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'         => 'Nom',
            'code'         => 'Code',
            'custodian'    => 'Gestionnaire d\'entrepôt',
            'assets-count' => 'Actifs stockés',
            'total-cost'   => 'Évaluation totale',
            'is-active'    => 'Actif',
            'company'      => 'Entreprise',
            'creator'      => 'Créé par',
            'deleted-at'   => 'Archivé le',
            'created-at'   => 'Créé le',
            'updated-at'   => 'Mis à jour le',
        ],

        'filters' => [
            'custodian' => 'Gestionnaire d\'entrepôt',
            'is-active' => 'Actif',
            'company'   => 'Entreprise',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Entrepôt mis à jour',
                    'body'  => 'L\'entrepôt a été mis à jour avec succès.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'error' => [
                        'title' => 'L\'entrepôt n\'a pas pu être supprimé',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'   => 'Détails de l\'entrepôt',
                'entries' => [
                    'name'      => 'Nom',
                    'code'      => 'Code',
                    'custodian' => 'Gestionnaire d\'entrepôt',
                    'company'   => 'Entreprise',
                    'is-active' => 'Actif',
                    'note'      => 'Adresse / Emplacement et remarques',
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
        'warehouse-assets' => [
            'title'   => 'Actifs stockés',
            'columns' => [
                'asset-tag'     => 'Étiquette d\'actif',
                'asset-name'    => 'Nom de l\'actif',
                'product'       => 'Produit',
                'model'         => 'Modèle',
                'serial-no'     => 'N° de série',
                'category'      => 'Catégorie',
                'condition'     => 'État',
                'cost'          => 'Coût',
                'purchase-date' => 'Date d\'achat',
            ],
            'actions' => [
                'issue' => 'Attribuer l\'actif',
            ],
        ],
    ],

    'pages' => [
        'list' => [
            'header-actions' => [
                'create' => [
                    'label'        => 'Nouvel entrepôt d\'actifs',
                    'notification' => [
                        'title' => 'Entrepôt créé',
                        'body'  => 'L\'entrepôt a été créé avec succès.',
                    ],
                ],
            ],
            'views' => [
                'all'      => 'Tous les entrepôts',
                'archived' => 'Entrepôts archivés',
            ],
        ],
        'create' => [
            'notification' => [
                'title' => 'Entrepôt créé',
                'body'  => 'L\'entrepôt a été créé avec succès.',
            ],
        ],
        'edit' => [
            'notification' => [
                'title' => 'Entrepôt mis à jour',
                'body'  => 'L\'entrepôt a été mis à jour avec succès.',
            ],
        ],
    ],
];
