<?php

return [
    'navigation' => [
        'title' => 'Transferts internes',
        'group' => 'Transferts',
    ],

    'global-search' => [
        'origin' => 'Origine',
    ],

    'table' => [
        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Transfert interne supprimé',
                        'body'  => 'Le transfert interne a été supprimé avec succès.',
                    ],

                    'error' => [
                        'title' => 'Le transfert interne n\'a pas pu être supprimé',
                        'body'  => 'Le transfert interne ne peut pas être supprimé car il est actuellement utilisé.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Transferts internes supprimés',
                        'body'  => 'Les transferts internes ont été supprimés avec succès.',
                    ],

                    'error' => [
                        'title' => 'Les transferts internes n\'ont pas pu être supprimés',
                        'body'  => 'Les transferts internes ne peuvent pas être supprimés car ils sont actuellement utilisés.',
                    ],
                ],
            ],
        ],
    ],
];
