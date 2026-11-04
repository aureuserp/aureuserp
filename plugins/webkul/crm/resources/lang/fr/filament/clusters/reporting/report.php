<?php

return [
    'none' => 'Aucun',

    'chart' => [
        'heading' => ':measure par :group',
    ],

    'filters' => [
        'measure'        => 'Mesure',
        'split-by'       => 'Répartir par',
        'no-split'       => 'Aucune répartition',
        'salespeople'    => 'Vendeurs',
        'teams'          => 'Équipes commerciales',
    ],

    'dimensions' => [
        'salesperson'         => 'Vendeur',
        'team'                => 'Équipe commerciale',
        'stage'               => 'Étape',
        'type'                => 'Type',
        'city'                => 'Ville',
        'country'             => 'Pays',
        'lost-reason'         => 'Motif de perte',
        'company'             => 'Société',
        'campaign'            => 'Campagne',
        'medium'              => 'Support',
        'source'              => 'Source',
        'expected-closing'    => 'Clôture prévue',
        'created-at'          => 'Date de création',
        'converted-at'        => 'Date de conversion',
        'closed-at'           => 'Date de clôture',
        'activity-type'       => 'Type d\'activité',
        'assigned-to'         => 'Assigné à',
        'done-by'             => 'Réalisée par',
        'done-at'             => 'Date de réalisation',
        'activity-created-at' => 'Date de création de l\'activité',
    ],

    'measures' => [
        'count'            => 'Nombre',
        'expected-revenue' => 'Revenu attendu',
        'prorated-revenue' => 'Revenu au prorata',
        'probability'      => 'Probabilité (moy.)',
        'days-to-assign'   => 'Jours avant assignation',
        'days-to-close'    => 'Jours avant clôture',
    ],

    'presets' => [
        'ongoing'           => 'En cours',
        'won'               => 'Gagnées',
        'lost'              => 'Perdues',
    ],

    'table' => [
        'lost' => 'Perdue',

        'columns' => [
            'name'             => 'Nom',
            'type'             => 'Type',
            'customer'         => 'Client',
            'stage'            => 'Étape',
            'expected-revenue' => 'Revenu attendu',
            'probability'      => 'Probabilité',
            'salesperson'      => 'Vendeur',
            'team'             => 'Équipe commerciale',
            'expected-closing' => 'Clôture prévue',
            'created-at'       => 'Créé le',
            'closed-at'        => 'Clôturé le',
            'lost-reason'      => 'Motif de perte',
            'country'          => 'Pays',
            'city'             => 'Ville',
            'done-at'          => 'Réalisée le',
            'activity-type'    => 'Type d\'activité',
            'summary'          => 'Résumé',
            'lead'             => 'Piste / Opportunité',
            'assigned-to'      => 'Assigné à',
            'done-by'          => 'Réalisée par',
            'due-date'         => 'Date d\'échéance',
        ],
    ],

    'rules' => [
        'active' => [
            'label'         => 'Actives',
            'is-active'     => 'Est actif',
            'is-not-active' => 'N\'est pas actif',
            'not-active'    => 'Non actives',
        ],

        'won' => [
            'label'      => 'Gagnées',
            'is-won'     => 'Est gagnée',
            'is-not-won' => 'N\'est pas gagnée',
            'not-won'    => 'Non gagnées',
        ],

        'lost' => [
            'label'       => 'Perdues',
            'is-lost'     => 'Est perdue',
            'is-not-lost' => 'N\'est pas perdue',
            'not-lost'    => 'Non perdues',
        ],
    ],

    'tabs' => [
        'leads'         => 'Pistes',
        'opportunities' => 'Opportunités',
        'mine'          => 'Les miennes',
        'won'           => 'Gagnées',
        'lost'          => 'Perdues',
        'my-activities' => 'Mes activités',
        'this-month'    => 'Ce mois-ci',
    ],
];
