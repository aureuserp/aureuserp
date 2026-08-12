<?php

return [
    'navigation' => [
        'title' => 'Réceptions',
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
                        'title' => 'Réception supprimée',
                        'body'  => 'La réception a été supprimée avec succès.',
                    ],

                    'error' => [
                        'title' => 'La réception n\'a pas pu être supprimée',
                        'body'  => 'La réception ne peut pas être supprimée car elle est actuellement utilisée.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Réceptions supprimées',
                        'body'  => 'Les réceptions ont été supprimées avec succès.',
                    ],

                    'error' => [
                        'title' => 'Les réceptions n\'ont pas pu être supprimées',
                        'body'  => 'Les réceptions ne peuvent pas être supprimées car elles sont actuellement utilisées.',
                    ],
                ],
            ],
        ],
    ],
];
