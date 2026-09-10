<?php

return [
    'columns' => [
        'number'           => 'Numéro',
        'date'             => 'Date',
        'account'          => 'Compte',
        'partner'          => 'Partenaire',
        'label'            => 'Libellé',
        'reference'        => 'Référence',
        'journal'          => 'Journal',
        'debit'            => 'Débit',
        'credit'           => 'Crédit',
        'balance'          => 'Solde',
        'currency'         => 'Devise',
        'company'          => 'Société',
        'status'           => 'Statut',
        'amount-currency'  => 'Montant en devise',
        'amount-residual'  => 'Montant résiduel',
        'reconciled'       => 'Lettré',
        'due-date'         => 'Date d\'échéance',
    ],

    'values' => [
        'yes' => 'Oui',
        'no'  => 'Non',
    ],

    'notification' => [
        'completed' => 'Votre export des éléments comptables est terminé et :count ligne(s) ont été exportées.',
        'failed'    => ':count ligne(s) n\'ont pas pu être exportées.',
    ],
];
