<?php

return [
    'navigation' => [
        'title' => 'Ventes',
    ],

    'navigation-group' => [
        'title' => 'Tableau de Bord',
    ],

    'filters-form' => [
        'start-date'     => 'Date de Début',
        'end-date'       => 'Date de Fin',
        'salesperson'    => 'Vendeur',
        'country'        => 'Pays',
        'product'        => 'Produit',
        'customer'       => 'Client',
        'category'       => 'Catégorie',
        'salesteam'      => 'Équipe de Vente',
    ],

    'widgets' => [
        'stats-overview' => [
            'heading'          => 'Aperçu des Ventes',
            'quotation'        => 'Devis',
            'order'            => 'Commande',
            'draft'            => 'Devis Brouillon',
            'cancel'           => 'Annuler le Devis',
            'total-revenue'    => 'Revenu Total',
            'avg-revenue'      => 'Revenu Moyen',
            'fully-invoiced'   => 'Entièrement Facturé',
            'archived'         => 'Archivé',
            'no-change'        => 'Aucun Changement',
            'increase'         => 'Augmentation',
            'decrease'         => 'Diminution',

            'descriptions' => [
                'quotation'     => 'Devis envoyés aux clients',
                'order'         => 'Commandes confirmées par les clients',
                'draft'         => 'Devis en brouillon',
                'cancel'        => 'Devis annulé par les clients',
                'total-revenue' => 'Revenu total des commandes',
                'avg-revenue'   => 'Revenu moyen des commandes',
                'fully-invoiced'=> 'Commandes entièrement facturées',
                'archived'      => 'Commandes archivées',
            ],
        ],

        'sales-chart' => [
            'heading'          => 'Graphique des Ventes',
            'confirmed-orders' => 'Commandes Confirmées',
            'draft-orders'     => 'Commandes en Brouillon',
            'sent-orders'      => 'Devis Envoyés',
            'cancelled-orders' => 'Commandes Annulées',
        ],

        'revenue-chart' => [
            'heading' => 'Graphique des Revenus',
            'label'   => 'Revenu',
        ],

        'yearly-comparison' => [
            'heading' => 'Comparaison Annuelle des Ventes',
            'label'   => 'Ventes',
        ],

        'top-categories' => [
            'heading' => 'Meilleures Catégories',
            'column'  => [
                'category'              => 'Catégorie',
                'category_full_name'    => 'Nom Complet',
                'product_count'         => 'Nombre de Produits',
            ],
        ],

        'top-customers' => [
            'heading' => 'Meilleurs Clients',
            'column'  => [
                'customer'      => 'Client',
                'total_orders'  => 'Total des Commandes',
                'total_revenue' => 'Revenu Total',
            ],
        ],

        'top-products' => [
            'heading' => 'Meilleurs Produits',
            'column'  => [
                'product'       => 'Produit',
                'qty_sold'      => 'Quantité Vendue',
                'total_revenue' => 'Revenu Total',
            ],
        ],

        'top-sales-teams' => [
            'heading' => 'Meilleures Équipes de Vente',
            'column'  => [
                'sales_team'    => 'Équipe de Vente',
                'total_orders'  => 'Total des Commandes',
                'total_revenue' => 'Revenu',
            ],
        ],

        'top-sales-orders' => [
            'heading' => 'Meilleures Commandes',
            'column'  => [
                'order'         => 'Commande',
                'customer'      => 'Client',
                'order_date'    => 'Date de Commande',
                'total_amount'  => 'Montant Total',
            ],
        ],

        'top-sales-countries' => [
            'heading' => 'Meilleurs Pays de Vente',
            'column'  => [
                'country'        => 'Pays',
                'total_products' => 'Total des Produits',
                'total_revenue'  => 'Revenu Total',
            ],
        ],
    ],
];
