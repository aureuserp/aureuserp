<?php

return [
    'navigation' => [
        'title' => 'Livraisons directes',
        'group' => 'Transferts',
    ],

    'global-search' => [
        'partner' => 'Partenaire',
        'origin'  => 'Origine',
    ],

    'table' => [
        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Livraison directe supprimée',
                        'body'  => 'La livraison directe a été supprimée avec succès.',
                    ],

                    'error' => [
                        'title' => 'La livraison directe n\'a pas pu être supprimée',
                        'body'  => 'La livraison directe ne peut pas être supprimée car elle est actuellement utilisée.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Livraisons directes supprimées',
                        'body'  => 'Les livraisons directes ont été supprimées avec succès.',
                    ],

                    'error' => [
                        'title' => 'Les livraisons directes n\'ont pas pu être supprimées',
                        'body'  => 'Les livraisons directes ne peuvent pas être supprimées car elles sont actuellement utilisées.',
                    ],
                ],
            ],
        ],
    ],
];
