<?php

return [
    'columns' => [
        'occurred-at'        => 'Date et heure',
        'asset-tag'          => 'Étiquette de l\'actif',
        'product'            => 'Produit',
        'model'              => 'Modèle',
        'category'           => 'Catégorie',
        'serial-no'          => 'Numéro de série',
        'type'               => 'Type de mouvement',
        'quantity'           => 'Quantité',
        'source-type'        => 'Type de source',
        'from'               => 'De',
        'to'                 => 'À',
        'condition'          => 'État',
        'expected-return-at' => 'Date de retour prévue',
        'performed-by'       => 'Effectué par',
        'note'               => 'Note',
        'company'            => 'Société',
    ],

    'options' => [
        'section'    => 'Filtrer les mouvements exportés (Optionnel)',
        'date-from'  => 'Date de début',
        'date-until' => 'Date de fin',
        'types'      => 'Types de mouvement',
        'sites'      => 'Sites',
        'employees'  => 'Employés',
    ],

    'notification' => [
        'completed' => 'Votre export de mouvements d\'actifs est terminé et :count ligne(s) exportée(s).',
        'failed'    => ':count ligne(s) n\'ont pas pu être exportée(s).',
    ],
];
