<?php

return [
    'notification' => [
        'title' => 'Rebut mis à jour',
        'body'  => 'Le rebut a été mis à jour avec succès.',
    ],

    'header-actions' => [
        'validate' => [
            'label' => 'Valider',

            'notification' => [
                'warning' => [
                    'title' => 'Stock insuffisant',
                    'body'  => 'Le rebut a un stock insuffisant pour être validé.',
                ],

                'success' => [
                    'title' => 'Rebut marqué comme terminé',
                    'body'  => 'Le rebut a été marqué comme terminé avec succès.',
                ],
            ],
        ],

        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Rebut supprimé',
                    'body'  => 'Le rebut a été supprimé avec succès.',
                ],

                'error' => [
                    'title' => 'Les rebuts n\'ont pas pu être supprimés',
                    'body'  => 'Les rebuts ne peuvent pas être supprimés car ils sont actuellement utilisés.',
                ],
            ],
        ],
    ],
];
