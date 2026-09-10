<?php

return [
    'assets' => [
        'label'   => 'Actifs',
        'options' => [
            'receivable'  => 'Créances',
            'cash'        => 'Banque et caisse',
            'current'     => 'Actifs circulants',
            'non-current' => 'Actifs non courants',
            'prepayments' => 'Charges constatées d\'avance',
            'fixed'       => 'Immobilisations',
        ],
    ],

    'liabilities' => [
        'label'   => 'Passifs',
        'options' => [
            'payable'     => 'Dettes',
            'credit-card' => 'Carte de crédit',
            'current'     => 'Passifs circulants',
            'non-current' => 'Passifs non courants',
        ],
    ],

    'equity' => [
        'label'   => 'Capitaux propres',
        'options' => [
            'equity'     => 'Capitaux propres',
            'unaffected' => 'Résultat de l\'exercice en cours',
        ],
    ],

    'income' => [
        'label'   => 'Produits',
        'options' => [
            'income' => 'Produits',
            'other'  => 'Autres produits',
        ],
    ],

    'expenses' => [
        'label'   => 'Charges',
        'options' => [
            'expense'      => 'Charges',
            'depreciation' => 'Amortissement',
            'direct-cost'  => 'Coût des revenus',
        ],
    ],

    'off-balance' => [
        'label'   => 'Hors bilan',
        'options' => [
            'off-balance' => 'Hors bilan',
        ],
    ],
];
