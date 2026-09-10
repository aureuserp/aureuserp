<?php

return [
    'navigation' => [
        'title' => 'Accords d\'achat',
        'group' => 'Achat',
    ],

    'global-search' => [
        'vendor' => 'Fournisseur',
        'type'   => 'Type',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Général',

                'fields' => [
                    'vendor'                => 'Fournisseur',
                    'valid-from'            => 'Valable à partir de',
                    'valid-to'              => 'Valable jusqu\'au',
                    'buyer'                 => 'Acheteur',
                    'reference'             => 'Référence',
                    'reference-placeholder' => 'ex. PO/123',
                    'agreement-type'        => 'Type d\'accord',
                    'company'               => 'Société',
                    'currency'              => 'Devise',
                ],
            ],
        ],

        'tabs' => [
            'products' => [
                'title' => 'Produits',

                'columns' => [
                    'product'    => 'Produit',
                    'quantity'   => 'Quantité',
                    'ordered'    => 'Commandé',
                    'uom'        => 'Unité de mesure',
                    'unit-price' => 'Prix unitaire',
                ],

                'fields' => [
                    'product'    => 'Produit',
                    'quantity'   => 'Quantité',
                    'ordered'    => 'Commandé',
                    'uom'        => 'Unité de mesure',
                    'unit-price' => 'Prix unitaire',
                ],
            ],

            'additional' => [
                'title' => 'Informations complémentaires',
            ],

            'terms' => [
                'title' => 'Termes et conditions',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'agreement'      => 'Accord',
            'vendor'         => 'Fournisseur',
            'agreement-type' => 'Type d\'accord',
            'buyer'          => 'Acheteur',
            'company'        => 'Société',
            'valid-from'     => 'Valable à partir de',
            'valid-to'       => 'Valable jusqu\'au',
            'reference'      => 'Référence',
            'status'         => 'Statut',
        ],

        'groups' => [
            'agreement-type' => 'Type d\'accord',
            'vendor'         => 'Fournisseur',
            'state'          => 'État',
            'created-at'     => 'Créé le',
            'updated-at'     => 'Mis à jour le',
        ],

        'filters' => [
            'agreement'      => 'Accord',
            'vendor'         => 'Fournisseur',
            'agreement-type' => 'Type d\'accord',
            'buyer'          => 'Acheteur',
            'company'        => 'Société',
            'valid-from'     => 'Valable à partir de',
            'valid-to'       => 'Valable jusqu\'au',
            'reference'      => 'Référence',
            'status'         => 'Statut',
            'created-at'     => 'Créé le',
            'updated-at'     => 'Mis à jour le',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Accord d\'achat supprimé',
                    'body'  => 'L\'accord d\'achat a été supprimé avec succès.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Accord d\'achat restauré',
                    'body'  => 'L\'accord d\'achat a été restauré avec succès.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Accord d\'achat définitivement supprimé',
                        'body'  => 'L\'accord d\'achat a été définitivement supprimé avec succès.',
                    ],

                    'error' => [
                        'title' => 'L\'accord d\'achat n\'a pas pu être supprimé',
                        'body'  => 'L\'accord d\'achat ne peut pas être supprimé car il est actuellement utilisé.',
                    ],

                    'warning' => [
                        'title' => 'L\'accord d\'achat ne peut pas être supprimé',
                        'body'  => 'Seuls les accords d\'achat en statut Brouillon ou Annulé peuvent être supprimés.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Accords d\'achat supprimés',
                    'body'  => 'Les accords d\'achat ont été supprimés avec succès.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Accords d\'achat restaurés',
                    'body'  => 'Les accords d\'achat ont été restaurés avec succès.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Accords d\'achat définitivement supprimés',
                        'body'  => 'Les accords d\'achat ont été définitivement supprimés avec succès.',
                    ],

                    'error' => [
                        'title' => 'Les accords d\'achat n\'ont pas pu être supprimés',
                        'body'  => 'Les accords d\'achat ne peuvent pas être supprimés car ils sont actuellement utilisés.',
                    ],

                    'warning' => [
                        'title' => 'L\'accord d\'achat ne peut pas être supprimé',
                        'body'  => 'Seuls les accords d\'achat en statut Brouillon ou Annulé peuvent être supprimés.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Général',

                'entries' => [
                    'vendor'                => 'Fournisseur',
                    'valid-from'            => 'Valable à partir de',
                    'valid-to'              => 'Valable jusqu\'au',
                    'buyer'                 => 'Acheteur',
                    'reference'             => 'Référence',
                    'reference-placeholder' => 'ex. PO/123',
                    'agreement-type'        => 'Type d\'accord',
                    'company'               => 'Société',
                    'currency'              => 'Devise',
                ],
            ],

            'metadata' => [
                'title' => 'Métadonnées',

                'entries' => [
                    'created-at' => 'Créé le',
                    'created-by' => 'Créé par',
                    'updated-at' => 'Mis à jour le',
                ],
            ],
        ],

        'tabs' => [
            'products' => [
                'title' => 'Produits',

                'entries' => [
                    'product'    => 'Produit',
                    'quantity'   => 'Quantité',
                    'ordered'    => 'Commandé',
                    'uom'        => 'Unité de mesure',
                    'unit-price' => 'Prix unitaire',
                ],
            ],

            'additional' => [
                'title' => 'Informations complémentaires',
            ],

            'terms' => [
                'title' => 'Termes et conditions',
            ],
        ],
    ],
];
