<?php

return [
    'none' => 'Aucun',

    'views' => [
        'kanban'  => 'Kanban',
        'graph'   => 'Graphique',
        'summary' => 'Synthèse',
        'list'    => 'Liste',
    ],

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
        'my-pipeline'       => 'Mon pipeline',
        'unassigned'        => 'Non assignées',
        'opportunities'     => 'Opportunités',
        'leads'             => 'Pistes',
        'upcoming-closings' => 'Clôtures à venir',
        'active'            => 'Actives',
        'ongoing'           => 'En cours',
        'won'               => 'Gagnées',
        'lost'              => 'Perdues',
        'created-this-year' => 'Créées cette année',
        'closed-this-year'  => 'Clôturées cette année',
        'last-12-months'    => '12 derniers mois',
        'my-activities'     => 'Mes activités',
        'done-by-me'        => 'Réalisées par moi',
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

    'empty-state' => [
        'heading'     => 'Aucune donnée pour le moment',
        'description' => 'Essayez de retirer certains filtres.',
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
