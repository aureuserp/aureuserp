<?php

return [
    'navigation' => [
        'title' => 'Produits',
    ],

    'label'        => 'Produit',
    'plural-label' => 'Produits',

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Informations Générales',
                'fields' => [
                    'name'                 => 'Nom du Produit',
                    'category'             => 'Catégorie',
                    'type'                 => 'Type de Produit',
                    'type-asset-hint'      => 'Une unité suivie identifiée par un numéro de série et un détenteur.',
                    'type-consumable-hint' => 'Un lot de quantité émis par unités sans suivi de retour.',
                    'model'                => 'Modèle',
                    'cost'                 => 'Coût Standard',
                    'is-active'            => 'Actif',
                    'note'                 => 'Description / Spécifications',
                ],
            ],

            'settings' => [
                'title'  => 'Paramètres',
                'fields' => [
                    'company' => 'Société',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'   => 'Informations Générales',
                'entries' => [
                    'name'      => 'Nom du Produit',
                    'category'  => 'Catégorie',
                    'type'      => 'Type de Produit',
                    'model'     => 'Modèle',
                    'cost'      => 'Coût Standard',
                    'is-active' => 'Actif',
                    'note'      => 'Description / Spécifications',
                ],
            ],

            'settings' => [
                'title'   => 'Paramètres',
                'entries' => [
                    'company' => 'Société',
                ],
            ],

            'audit' => [
                'title'   => 'Informations d\'Audit',
                'entries' => [
                    'creator'    => 'Créé par',
                    'created-at' => 'Créé le',
                    'updated-at' => 'Mis à jour le',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'                    => 'Nom du Produit',
            'category'                => 'Catégorie',
            'type'                    => 'Type de Produit',
            'model'                   => 'Modèle',
            'cost'                    => 'Coût',
            'assets-count'            => 'Actifs Enregistrés',
            'available-assets-count'  => 'Disponible en entrepôt',
            'held-assets-count'       => 'Attribué en garde',
            'terminated-assets-count' => 'Mis au rebut / Perdu',
            'total-cost'              => 'Évaluation totale',
            'is-active'               => 'Actif',
            'company'                 => 'Société',
            'deleted-at'              => 'Archivé le',
            'created-at'              => 'Créé le',
            'updated-at'              => 'Mis à jour le',
        ],

        'groups' => [
            'category' => 'Catégorie',
        ],

        'filters' => [
            'category'  => 'Catégorie',
            'type'      => 'Type de Produit',
            'is-active' => 'Actif',
            'company'   => 'Société',
        ],
    ],

    'views' => [
        'active'   => 'Produits Actifs',
        'archived' => 'Produits Archivés',
    ],

    'relation-manager' => [
        'assets' => [
            'title'   => 'Unités d\'Actifs Enregistrées',
            'actions' => [
                'open' => 'Voir l\'Actif',
            ],
        ],
    ],
];
