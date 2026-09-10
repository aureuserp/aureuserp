<?php

return [
    'title' => 'Gérer la facturation',

    'breadcrumb' => 'Gérer la facturation',

    'navigation' => [
        'title' => 'Gérer la facturation',
    ],

    'form' => [
        'invoice-policy' => [
            'label'      => 'Politique de facturation',
            'label-help' => 'Définissez comment les factures sont générées à partir des commandes clients.',
            'options'    => [
                'order'    => 'Générer la facture en fonction des quantités commandées',
                'delivery' => 'Générer la facture en fonction des quantités livrées',
            ],
        ],
    ],
];
