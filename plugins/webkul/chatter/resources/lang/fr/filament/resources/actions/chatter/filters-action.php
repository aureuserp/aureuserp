<?php

return [
    'tooltip' => 'Filtres',

    'fields'  => [
        'search'             => 'Rechercher',
        'search-placeholder' => 'Rechercher des messages...',
        'type'               => 'Type',
        'date'               => 'Date',
        'sort-by'            => 'Trier par',
        'pinned-only'        => 'Épinglés uniquement',
    ],
    'type-options' => [
        'all'          => 'Tous les types',
        'note'         => 'Notes',
        'comment'      => 'Commentaires',
        'notification' => 'Notifications',
        'activity'     => 'Activités',
    ],
    'date-options' => [
        ''          => 'À tout moment',
        'today'     => 'Aujourd\'hui',
        'yesterday' => 'Hier',
        'week'      => '7 derniers jours',
        'month'     => '30 derniers jours',
        'quarter'   => '3 derniers mois',
        'year'      => 'L\'année dernière',
    ],
    'sort-options' => [
        'created_at_desc' => 'Plus récent en premier',
        'created_at_asc'  => 'Plus ancien en premier',
        'updated_at_desc' => 'Récemment mis à jour',
        'priority'        => 'Priorité',
    ],
    'actions' => [
        'apply' => 'Appliquer les filtres',
    ],
];
