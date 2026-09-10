<?php

return [
    'breadcrumb' => 'Gérer la devise',
    'title'      => 'Gérer la devise',
    'group'      => 'Général',

    'navigation' => [
        'label' => 'Gérer la devise',
    ],

    'form' => [
        'default-currency' => [
            'label'       => 'Devise de base',
            'helper-text' => 'La devise de base du système. Les prix des produits sont stockés dans cette devise et sont convertis dans la devise de la société lors d’une vente ou d’un achat.',
        ],
    ],
];
