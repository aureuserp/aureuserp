<?php

return [
    'navigation' => [
        'title' => 'Rebuts',
        'group' => 'Ajustements',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Général',

                'fields' => [
                    'product'              => 'Produit',
                    'package'              => 'Colis',
                    'quantity'             => 'Quantité',
                    'unit'                 => 'Unité de mesure',
                    'lot'                  => 'Lot/Série',
                    'tags'                 => 'Étiquettes',
                    'name'                 => 'Nom',
                    'color'                => 'Couleur',
                    'owner'                => 'Propriétaire',
                    'source-location'      => 'Emplacement source',
                    'destination-location' => 'Emplacement de rebut',
                    'source-document'      => 'Document source',
                    'company'              => 'Société',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'date'            => 'Date',
            'reference'       => 'Référence',
            'product'         => 'Produit',
            'package'         => 'Colis',
            'quantity'        => 'Quantité',
            'uom'             => 'Unité de mesure',
            'source-location' => 'Emplacement source',
            'scrap-location'  => 'Emplacement de rebut',
            'unit'            => 'Unité de mesure',
            'lot'             => 'Lot/Série',
            'tags'            => 'Étiquettes',
            'state'           => 'État',
        ],

        'groups' => [
            'product'              => 'Produit',
            'source-location'      => 'Emplacement source',
            'destination-location' => 'Emplacement de rebut',
        ],

        'filters' => [
            'source-location'      => 'Emplacement source',
            'destination-location' => 'Emplacement de rebut',
            'product'              => 'Produit',
            'state'                => 'État',
            'product-category'     => 'Catégorie de produit',
            'uom'                  => 'Unité de mesure',
            'lot'                  => 'Lot/Série',
            'package'              => 'Colis',
            'tags'                 => 'Étiquettes',
            'company'              => 'Société',
            'quantity'             => 'Quantité',
            'creator'              => 'Créateur',
            'closed-at'            => 'Clôturé le',
            'created-at'           => 'Créé le',
            'updated-at'           => 'Mis à jour le',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Rebut supprimé',
                        'body'  => 'Le rebut a été supprimé avec succès.',
                    ],

                    'error' => [
                        'title' => 'Le rebut n\'a pas pu être supprimé',
                        'body'  => 'Le rebut ne peut pas être supprimé car il est actuellement utilisé.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Rebuts supprimés',
                        'body'  => 'Les rebuts sélectionnés ont été supprimés avec succès.',
                    ],

                    'error' => [
                        'title' => 'Les rebuts n\'ont pas pu être supprimés',
                        'body'  => 'Les rebuts ne peuvent pas être supprimés car ils sont actuellement utilisés.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Détails du rebut',

                'entries' => [
                    'product'              => 'Produit',
                    'quantity'             => 'Quantité',
                    'lot'                  => 'Lot',
                    'tags'                 => 'Étiquettes',
                    'package'              => 'Colis',
                    'owner'                => 'Propriétaire',
                    'source-location'      => 'Emplacement source',
                    'destination-location' => 'Emplacement de rebut',
                    'source-document'      => 'Document source',
                    'company'              => 'Société',
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
