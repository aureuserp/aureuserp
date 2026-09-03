<?php

return [
    'title' => 'Gérer les taxes',

    'form' => [
        'default-taxes' => [
            'label'       => 'Taxes par défaut',
            'helper-text' => 'La taxe par défaut sera appliquée aux produits si aucune taxe n\'est sélectionnée',
        ],

        'sales-tax' => [
            'label' => 'Taxe de vente',
        ],

        'purchase-tax' => [
            'label' => 'Taxe d\'achat',
        ],

        'prices' => [
            'label' => 'Prix',
        ],

        'rounding-method' => [
            'label'       => 'Méthode d\'arrondi',
            'helper-text' => 'Méthode utilisée pour arrondir les montants de taxe',

            'options' => [
                'round-per-line' => 'Arrondir par ligne',
                'round-globally' => 'Arrondir globalement',
            ],
        ],

        'fiscal-country' => [
            'label' => 'Pays fiscal',
        ],
    ],
];
