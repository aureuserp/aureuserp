<?php

return [
    'columns' => [
        'date'            => 'Date',
        'name'            => 'Nom',
        'journal'         => 'Journal',
        'payment-method'  => 'Mode de paiement',
        'partner'         => 'Partenaire',
        'amount-currency' => 'Devise du montant',
        'amount'          => 'Montant',
        'state'           => 'État',
        'company'         => 'Société',
    ],

    'notification' => [
        'completed' => 'Votre export de paiements est terminé et :count ligne(s) exportée(s).',
        'failed'    => ':count ligne(s) n\'ont pas pu être exportée(s).',
    ],
];
