<?php

return [
    'model-label'        => 'Équipe commerciale',
    'plural-model-label' => 'Équipes commerciales',

    'navigation' => [
        'title' => 'Équipes',
    ],

    'table' => [
        'columns' => [
            'name'               => 'Équipe commerciale',
            'open-opportunities' => 'Opportunités ouvertes',
            'expected-revenue'   => 'Revenu attendu',
            'members'            => 'Membres',
            'company'            => 'Société',
        ],

        'filters' => [
            'team-leader' => 'Chef d\'équipe',
            'created-at'  => 'Créé le',
        ],

        'actions' => [
            'pipeline' => [
                'label' => 'Pipeline',
            ],
        ],
    ],

    'grid' => [
        'new-opportunities'  => '{1} :count nouvelle opportunité|[0,*] :count nouvelles opportunités',
        'quotations'         => '{1} :count devis|[0,*] :count devis',
        'configuration'      => 'Configuration',
        'menu'               => 'Menu de l\'équipe',
        'open-opportunities' => '{1} :count opportunité ouverte|[0,*] :count opportunités ouvertes',
        'orders-to-invoice'  => '{1} :count commande à facturer|[0,*] :count commandes à facturer',
        'chart'              => 'Nouvelles opportunités par semaine',
        'target-progress'    => 'Facturé ce mois-ci : :invoiced sur :target',
    ],

    'grid-menu' => [
        'view'          => 'Afficher',
        'reporting'     => 'Rapports',
        'leads'         => 'Pistes',
        'opportunities' => 'Opportunités',
        'quotations'    => 'Devis',
        'sales-orders'  => 'Bons de commande',
        'forecast'      => 'Prévisions',
        'activities'    => 'Activités',
        'no-color'      => 'Aucune couleur',
    ],
];
