<?php

return [
    'model-label'        => 'Plan récurrent',
    'plural-model-label' => 'Plans récurrents',

    'navigation' => [
        'title' => 'Plans récurrents',
    ],

    'header-actions' => [
        'create' => 'Nouveau plan récurrent',
    ],

    'form' => [
        'name'             => 'Nom du plan',
        'name-placeholder' => 'par ex. Mensuel',
        'months'           => 'Nombre de mois',
        'months-helper'    => 'Nombre de mois couverts par le revenu récurrent (Mensuel = 1, Annuel = 12).',
    ],

    'table' => [
        'columns' => [
            'name'          => 'Plan',
            'months'        => 'Mois',
            'opportunities' => 'Opportunités',
        ],
    ],

    'actions' => [
        'archive' => 'Archiver',
    ],

    'tabs' => [
        'active'   => 'Actifs',
        'archived' => 'Archivés',
    ],

    'notifications' => [
        'created'  => 'Plan récurrent créé',
        'updated'  => 'Plan récurrent mis à jour',
        'archived' => 'Plan récurrent archivé',
        'restored' => 'Plan récurrent restauré',
        'deleted'  => 'Plan récurrent supprimé',
    ],

    'opportunity' => [
        'recurring-revenue' => 'Revenu récurrent',
        'plan'              => 'Plan récurrent',
        'mrr'               => 'Revenu récurrent mensuel',
    ],
];
