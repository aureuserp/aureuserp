<?php

return [
    'notification' => [
        'title' => 'Catégorie mise à jour',
        'body'  => 'La catégorie a été mise à jour avec succès.',
    ],

    'header-actions' => [
        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Catégorie supprimée',
                    'body'  => 'La catégorie a été supprimée avec succès.',
                ],

                'error' => [
                    'title' => 'La catégorie n\'a pas pu être supprimée',
                    'body'  => 'La catégorie ne peut pas être supprimée car elle est actuellement utilisée.',
                ],
            ],
        ],
    ],

    'save' => [
        'notification' => [
            'error' => [
                'title' => 'Échec de la mise à jour de la catégorie',
            ],
        ],
    ],
];
