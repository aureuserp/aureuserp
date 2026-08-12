<?php

return [
    'columns' => [
        'invoice-date' => 'Date de facture',
        'date'         => 'Date',
        'number'       => 'Numéro',
        'partner'      => 'Partenaire',
        'reference'    => 'Référence',
        'journal'      => 'Journal',
        'company'      => 'Société',
        'total'        => 'Total',
        'state'        => 'État',
        'checked'      => 'Vérifié',
    ],

    'values' => [
        'yes' => 'Oui',
        'no'  => 'Non',
    ],

    'notification' => [
        'completed' => 'Votre export des écritures comptables est terminé et :count ligne(s) ont été exportées.',
        'failed'    => ':count ligne(s) n\'ont pas pu être exportées.',
    ],
];
