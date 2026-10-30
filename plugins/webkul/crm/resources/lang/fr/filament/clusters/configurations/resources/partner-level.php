<?php

return [
    'model-label'        => 'Niveau',
    'plural-model-label' => 'Niveaux',

    'navigation' => [
        'title' => 'Niveaux',
        'group' => 'Membres',
    ],

    'header-actions' => [
        'create' => 'Nouveau niveau',
    ],

    'form' => [
        'name'          => 'Nom',
        'weight'        => 'Poids du niveau',
        'weight-helper' => 'Probabilité que les partenaires de ce niveau reçoivent des pistes. 0 signifie aucune.',
    ],

    'table' => [
        'columns' => [
            'name'     => 'Nom',
            'weight'   => 'Poids du niveau',
            'partners' => 'Contacts',
        ],
    ],

    'tabs' => [
        'active'   => 'Actifs',
        'archived' => 'Archivés',
    ],

    'notifications' => [
        'created'  => 'Niveau créé',
        'updated'  => 'Niveau mis à jour',
        'restored' => 'Niveau restauré',
        'deleted'  => 'Niveau supprimé',
    ],

    'contact' => [
        'section'       => 'Niveau du partenaire',
        'level'         => 'Niveau',
        'weight'        => 'Poids du niveau',
        'weight-helper' => 'Laissez vide pour utiliser le poids du niveau.',
    ],
];
