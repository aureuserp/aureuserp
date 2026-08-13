<?php

return [
    'notification' => [
        'title' => 'Allocation mise à jour',
        'body'  => 'L\'allocation a été mise à jour avec succès.',
    ],

    'header-actions' => [
        'delete' => [
            'notification' => [
                'title' => 'Allocation supprimée',
                'body'  => 'L\'allocation a été supprimée avec succès.',
            ],
        ],
        'approved' => [
            'title' => 'Approuvé',

            'notification' => [
                'title' => 'Allocation approuvée',
                'body'  => 'L\'allocation a été approuvée avec succès.',
            ],
        ],
        'refuse' => [
            'title' => 'Refuser',

            'notification' => [
                'title' => 'Allocation refusée',
                'body'  => 'L\'allocation a été refusée avec succès.',
            ],
        ],
        'mark-as-ready-to-confirm' => [
            'title' => 'Marquer comme prêt à confirmer',

            'notification' => [
                'title' => 'Marqué comme prêt à confirmer',
                'body'  => 'L\'allocation a été marquée comme prête à confirmer avec succès.',
            ],
        ],
    ],
];
