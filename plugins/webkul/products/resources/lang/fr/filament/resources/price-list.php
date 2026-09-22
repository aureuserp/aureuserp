<?php

return [
    'navigation' => [
        'title' => 'Listes de prix',
        'group' => 'Produits',
    ],

    'header-actions' => [
        'create' => [
            'label' => 'Nouvelle liste de prix',
        ],
    ],

    'form' => [
        'section' => [
            'general' => [
                'title' => 'Informations générales',

                'fields' => [
                    'name'     => 'Nom de la liste de prix',
                    'currency' => 'Devise',
                    'company'  => 'Société',
                    'status'   => 'Actif',
                ],
            ],

            'rules' => [
                'title'        => 'Règles de prix',
                'description'  => 'La première règle qui correspond à un produit détermine son prix. Les règles les plus précises l\'emportent sur les plus générales.',
                'add-rule'     => 'Ajouter une règle',
                'all-products' => 'Tous les produits',

                'actions' => [
                    'edit' => 'Modifier la règle',
                ],

                'columns' => [
                    'apply-to'     => 'Appliquer sur',
                    'target'       => 'S\'applique à',
                    'min-quantity' => 'Qté min.',
                    'type'         => 'Calcul du prix',
                    'price'        => 'Prix',
                    'period'       => 'Période',
                ],

                'formula' => [
                    'discount'     => 'remise',
                    'markup'       => 'majoration',
                    'rule-tip'     => ':base avec une :type de :discount % et :surcharge de frais supplémentaires'."\n".'Exemple : :amount * :factor + :surcharge → :total',
                    'rounding-tip' => 'Astuce : vous voulez arrondir à 9,99 ? Arrondissez à 10,00 et ajoutez des frais de -0,01.',
                ],

                'fields' => [
                    'apply-to-product'      => 'Produit',
                    'apply-to-category'     => 'Catégorie',
                    'all-products'          => 'Tous les produits',
                    'all-categories'        => 'Toutes les catégories',
                    'all-variants'          => 'Toutes les variantes',
                    'variant'               => 'Variante',
                    'on'                    => 'sur',
                    'sales-price'           => 'Prix de vente',
                    'apply-to'              => 'Appliquer à',
                    'product'               => 'Produit',
                    'category'              => 'Catégorie de produit',
                    'min-quantity'          => 'Qté min.',
                    'type'                  => 'Type de prix',
                    'fixed-price'           => 'Prix fixe',
                    'percent-price'         => 'Remise',
                    'percent-price-helper'  => 'Utilisez une valeur négative pour appliquer une majoration.',
                    'base'                  => 'Prix de base',
                    'base-price-list'       => 'Autre liste de prix',
                    'price-discount'        => 'Remise',
                    'price-discount-helper' => 'Utilisez une valeur négative pour appliquer une majoration.',
                    'price-markup'          => 'Majoration',
                    'price-round'           => 'Arrondir à',
                    'price-round-helper'    => 'Arrondit le prix à un multiple de cette valeur, après la remise et avant les frais supplémentaires.',
                    'price-surcharge'       => 'Frais supplémentaires',
                    'price-min-margin'      => 'Marge min.',
                    'price-max-margin'      => 'Marge max.',
                    'starts-at'             => 'Période de validité',
                    'ends-at'               => 'Date de fin',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'     => 'Liste de prix',
            'currency' => 'Devise',
            'company'  => 'Société',
            'rules'    => 'Règles',
            'status'   => 'Actif',
        ],

        'filters' => [
            'status'   => 'Actif',
            'currency' => 'Devise',
        ],
    ],
];
