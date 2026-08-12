<?php

return [
    'notification' => [
        'title' => 'Taxe mise à jour',
        'body'  => 'La taxe a été mise à jour avec succès.',
    ],

    'header-actions' => [
        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Taxe supprimée',
                    'body'  => 'La taxe a été supprimée avec succès.',
                ],

                'error' => [
                    'title' => 'La taxe n\'a pas pu être supprimée',
                    'body'  => 'La taxe ne peut pas être supprimée car elle est actuellement utilisée.',
                ],

                'invalid-repartition-lines' => [
                    'title' => 'Lignes de répartition invalides',
                ],
            ],
        ],
    ],
];
