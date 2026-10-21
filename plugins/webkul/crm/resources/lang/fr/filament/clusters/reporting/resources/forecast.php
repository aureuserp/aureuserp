<?php

return [
    'title' => 'Prévisions',

    'navigation' => [
        'title' => 'Prévisions',
    ],

    'kanban' => [
        'filters' => [
            'salesperson' => 'Vendeur',
            'team'        => 'Équipe commerciale',
            'period'      => 'Clôture prévue',
            'upcoming'    => 'Clôtures à venir',
            'all-dates'   => 'Toutes les dates',
            'all'         => 'Tous',
        ],

        'empty-state' => [
            'heading'     => 'Aucune opportunité à prévoir',
            'description' => 'Définissez une date de clôture prévue sur vos opportunités pour les voir ici.',
        ],

        'notifications' => [
            'moved'        => 'Date de clôture prévue mise à jour',
            'unauthorized' => 'Vous n\'êtes pas autorisé à modifier cette opportunité.',
        ],
    ],
];
