<?php

return [
    'navigation' => [
        'title' => 'Lots / Numéros de série',
        'group' => 'Inventaire',
    ],

    'global-search' => [
        'ref'     => 'Référence',
        'product' => 'Produit',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Général',

                'fields' => [
                    'name'                   => 'Nom',
                    'name-placeholder'       => 'ex. LOT/0001/20121',
                    'product'                => 'Produit',
                    'product-hint-tooltip'   => 'Le produit associé à ce lot/numéro de série. Il ne peut pas être modifié s\'il a déjà été déplacé.',
                    'company'                => 'Société',
                    'reference'              => 'Référence',
                    'reference-hint-tooltip' => 'Un numéro de référence interne, s\'il est différent du numéro de lot/série du fabricant.',
                    'description'            => 'Description',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'         => 'Nom',
            'product'      => 'Produit',
            'on-hand-qty'  => 'Quantité en stock',
            'reference'    => 'Référence interne',
            'created-at'   => 'Créé le',
            'updated-at'   => 'Mis à jour le',
        ],

        'groups' => [
            'product'        => 'Produit',
            'location'       => 'Emplacement',
            'created-at'     => 'Créé le',
        ],

        'filters' => [
            'product'  => 'Produit',
            'location' => 'Emplacement',
            'creator'  => 'Créateur',
            'company'  => 'Société',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Lot supprimé',
                        'body'  => 'Le lot a été supprimé avec succès.',
                    ],

                    'error' => [
                        'title' => 'Le lot n\'a pas pu être supprimé',
                        'body'  => 'Le lot ne peut pas être supprimé car il est actuellement utilisé.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'print' => [
                'label' => 'Imprimer le code-barres',
            ],

            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Lots supprimés',
                        'body'  => 'Les lots ont été supprimés avec succès.',
                    ],

                    'error' => [
                        'title' => 'Les lots n\'ont pas pu être supprimés',
                        'body'  => 'Les lots ne peuvent pas être supprimés car ils sont actuellement utilisés.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Détails du lot/numéro de série',

                'entries' => [
                    'name'        => 'Nom du lot',
                    'product'     => 'Produit',
                    'reference'   => 'Référence',
                    'description' => 'Description',
                    'on-hand-qty' => 'Quantité en stock',
                    'company'     => 'Société',
                    'created-at'  => 'Créé le',
                    'updated-at'  => 'Dernière mise à jour',
                ],
            ],

            'record-information' => [
                'title' => 'Informations sur l\'enregistrement',

                'entries' => [
                    'created-by'   => 'Créé par',
                    'created-at'   => 'Créé le',
                    'last-updated' => 'Dernière mise à jour',
                ],
            ],
        ],
    ],
];
