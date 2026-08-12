<?php

return [
    'navigation' => [
        'title' => 'Listes de prix fournisseur',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Général',

                'fields' => [
                    'vendor'                      => 'Fournisseur',
                    'vendor-product-name'         => 'Nom du produit chez le fournisseur',
                    'vendor-product-name-tooltip' => 'Le nom du produit du fournisseur apparaîtra sur la demande de prix. Laissez vide pour utiliser le nom interne du produit.',
                    'vendor-product-code'         => 'Code du produit chez le fournisseur',
                    'vendor-product-code-tooltip' => 'Le code du produit du fournisseur apparaîtra sur la demande de prix. Laissez vide pour utiliser le code interne.',
                    'delay'                       => 'Délai de livraison (jours)',
                    'delay-tooltip'               => 'Le délai (en jours) entre la confirmation du bon de commande et la réception du produit dans l\'entrepôt. Utilisé par le planificateur pour la planification automatique des bons de commande.',
                ],
            ],

            'prices' => [
                'title'  => 'Prix',

                'fields' => [
                    'product'            => 'Produit',
                    'quantity'           => 'Quantité',
                    'quantity-tooltip'   => 'La quantité minimale requise pour acheter auprès de ce fournisseur afin de bénéficier du prix indiqué. Exprimée dans l\'unité de mesure du produit du fournisseur ou, si non définie, l\'unité de mesure par défaut du produit.',
                    'unit-price'         => 'Prix unitaire',
                    'unit-price-tooltip' => 'Le prix par unité de ce produit chez le fournisseur, exprimé dans l\'unité de mesure du produit du fournisseur ou, si non définie, l\'unité de mesure par défaut du produit.',
                    'currency'           => 'Devise',
                    'valid-from'         => 'Valable à partir de',
                    'valid-to'           => 'Valable jusqu\'au',
                    'discount'           => 'Remise (%)',
                    'company'            => 'Société',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'vendor'              => 'Fournisseur',
            'vendor-product-name' => 'Nom du produit chez le fournisseur',
            'vendor-product-code' => 'Code du produit chez le fournisseur',
            'delay'               => 'Délai de livraison (jours)',
            'product'             => 'Produit',
            'quantity'            => 'Quantité',
            'unit-price'          => 'Prix unitaire',
            'currency'            => 'Devise',
            'valid-from'          => 'Valable à partir de',
            'valid-to'            => 'Valable jusqu\'au',
            'discount'            => 'Remise (%)',
            'company'             => 'Société',
            'created-at'          => 'Créé le',
            'updated-at'          => 'Mis à jour le',
        ],

        'filters' => [
            'vendor'        => 'Filtrer par fournisseur',
            'product'       => 'Filtrer par produit',
            'currency'      => 'Filtrer par devise',
            'company'       => 'Filtrer par société',
            'price-from'    => 'Prix minimum',
            'price-to'      => 'Prix maximum',
            'min-qty-from'  => 'Quantité minimale à partir de',
            'min-qty-to'    => 'Quantité minimale jusqu\'à',
            'starts-from'   => 'Date de début de validité',
            'ends-before'   => 'Date de fin de validité',
            'created-from'  => 'Créé à partir de',
            'created-until' => 'Créé jusqu\'à',
        ],

        'groups' => [
            'vendor'     => 'Fournisseur',
            'product'    => 'Produit',
            'created-at' => 'Créé le',
            'updated-at' => 'Mis à jour le',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Prix fournisseur supprimé',
                        'body'  => 'Le prix fournisseur a été supprimé avec succès.',
                    ],

                    'error' => [
                        'title' => 'Le prix fournisseur n\'a pas pu être supprimé',
                        'body'  => 'Le prix fournisseur ne peut pas être supprimé car il est actuellement utilisé.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Prix fournisseur supprimés',
                        'body'  => 'Les prix fournisseur ont été supprimés avec succès.',
                    ],

                    'error' => [
                        'title' => 'Les prix fournisseur n\'ont pas pu être supprimés',
                        'body'  => 'Les prix fournisseur ne peuvent pas être supprimés car ils sont actuellement utilisés.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'  => 'Général',

                'entries' => [
                    'vendor'                      => 'Fournisseur',
                    'vendor-product-name'         => 'Nom du produit chez le fournisseur',
                    'vendor-product-name-tooltip' => 'Le nom du produit du fournisseur apparaîtra sur la demande de prix. Laissez vide pour utiliser le nom interne du produit.',
                    'vendor-product-code'         => 'Code du produit chez le fournisseur',
                    'vendor-product-code-tooltip' => 'Le code du produit du fournisseur apparaîtra sur la demande de prix. Laissez vide pour utiliser le code interne.',
                    'delay'                       => 'Délai de livraison (jours)',
                    'delay-tooltip'               => 'Le délai (en jours) entre la confirmation du bon de commande et la réception du produit dans l\'entrepôt. Utilisé par le planificateur pour la planification automatique des bons de commande.',
                ],
            ],

            'record-information' => [
                'title'  => 'Informations sur l\'enregistrement',

                'entries' => [
                    'created-by'   => 'Créé par',
                    'created-at'   => 'Créé le',
                    'last-updated' => 'Dernière mise à jour',
                ],
            ],

            'prices' => [
                'title'  => 'Prix',

                'entries' => [
                    'product'            => 'Produit',
                    'quantity'           => 'Quantité',
                    'quantity-tooltip'   => 'La quantité minimale requise pour acheter auprès de ce fournisseur afin de bénéficier du prix indiqué. Exprimée dans l\'unité de mesure du produit du fournisseur ou, si non définie, l\'unité de mesure par défaut du produit.',
                    'unit-price'         => 'Prix unitaire',
                    'unit-price-tooltip' => 'Le prix par unité de ce produit chez le fournisseur, exprimé dans l\'unité de mesure du produit du fournisseur ou, si non définie, l\'unité de mesure par défaut du produit.',
                    'currency'           => 'Devise',
                    'valid-from'         => 'Valable à partir de',
                    'valid-to'           => 'Valable jusqu\'au',
                    'discount'           => 'Remise (%)',
                    'company'            => 'Société',
                ],
            ],
        ],
    ],
];
