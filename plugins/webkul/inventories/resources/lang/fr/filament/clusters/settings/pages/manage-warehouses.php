<?php

return [
    'title' => 'Gérer les entrepôts',

    'form' => [
        'enable-locations'                      => 'Emplacements',
        'enable-locations-helper-text'          => 'Suivez l\'emplacement des produits dans votre entrepôt',
        'configure-locations'                   => 'Configurer les emplacements',
        'enable-multi-steps-routes'             => 'Routes multi-étapes',
        'enable-multi-steps-routes-helper-text' => 'Utilisez vos propres routes pour gérer le transfert de produits entre entrepôts',
        'configure-routes'                      => 'Configurer les routes d\'entrepôt',
    ],

    'before-save' => [
        'notification' => [
            'warning' => [
                'title' => 'Plusieurs entrepôts',
                'body'  => 'Vous ne pouvez pas désactiver le multi-emplacement si vous avez plus d\'un entrepôt.',
            ],
        ],
    ],
];
